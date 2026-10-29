# Architecture

A one-page map of how DutyBoard is built. Read this before your first change.

> Parts of this describe the planned layout. Sections are marked **(planned)** until the code exists, and this page is updated as each slice lands.

## The stack

| Piece | What it does |
|---|---|
| **Laravel** (PHP) | The application: routing, rules, database access |
| **Laravel Fortify** | Sign-in, two-step sign-in, password changes. DutyBoard supplies the screens |
| **Livewire** *(arrives with truck checks)* | Pages that update as you type, written in PHP and Blade, with no separate JavaScript app |
| **PostgreSQL** | The only supported database |
| **Caddy** | Web server in front of the app. Gets and renews HTTPS certificates automatically |
| **Docker Compose** | Runs everything on one server |

There's no Node.js, npm, or front-end build step. Styles are plain CSS in `public/css/app.css`: edit it and reload. Livewire's small JavaScript file will ship with its Composer package.

Why these choices: see [decisions 0001–0003](decisions/).

## Containers

**Development** (`compose.yaml`, today): `app` runs Laravel's built-in server on port 8080, `worker` runs background jobs, and `postgres` holds the data. Your copy of the code is mounted into the containers, so edits show up immediately.

**Production** *(planned, with acceptance criterion A13)*: PHP-FPM behind Caddy, with HTTPS:

```
            internet
               │  443
          ┌────▼────┐
          │  caddy  │  HTTPS, serves the app
          └────┬────┘
          ┌────▼────┐        ┌──────────┐
          │   app   │───────▶│ postgres │  data, never exposed outside
          └─────────┘        └────▲─────┘
          ┌─────────┐             │
          │ worker  │─────────────┘  sends texts, runs scheduled jobs and backups
          └─────────┘
```

`app` and `worker` run from the same image. Only Caddy is reachable from outside the server.

## How a request flows

A working example from the code today: an administrator deactivates someone.

1. The People screen posts to `/admin/users/{user}/deactivate`. **`routes/web.php`** sends it to `Admin\UserController@deactivate`.
2. The controller checks permission with **`App\Policies\UserPolicy`** (administrators only).
3. It hands the work to the **action** **`App\Actions\Users\DeactivateUser`**, which holds the rule ("you can't deactivate yourself"), saves the change, and writes an **audit log** entry.
4. The controller redirects back with a message, shown by **`resources/views/partials/messages.blade.php`**.
5. On that person's next click, **`App\Http\Middleware\EnsureAccountIsActive`** signs them out.

Truck checks will follow the same shape, with a Livewire component in place of the controller *(planned)*:

1. The browser posts to `/truck-checks`. **`routes/web.php`** sends it to a Livewire component.
2. The component in **`app/Livewire/`** validates the input using the rules in **`app/Rules/`** (for example, "a Fail needs a note").
3. The work is done by an **action** in **`app/Actions/`**, such as `SubmitTruckCheck`. Actions are where business rules live. Each one does one thing and is named for it.
4. The action saves through **models** in **`app/Models/`** (`TruckCheck`, `Deficiency`) and writes an **audit log** entry.
5. Anything slow, like sending texts, is **queued** as a job in **`app/Jobs/`**, and the `worker` container runs it.
6. The component re-renders its **Blade view** from **`resources/views/livewire/`**.

Rule of thumb: **components handle the screen, actions handle the rules.** If you're looking for "why can't a crew member do X", look in `app/Actions/` and `app/Policies/`.

## Folders that matter

| Folder | What's in it |
|---|---|
| `app/Enums/` | Fixed lists with rules attached: `Role` and `Certification` (which certification can fill which seat) |
| `app/Models/` | One class per table: `User`, `Station`, `Unit`, `AuditLog`, and later `TruckCheck`, `MileageEntry`, `Assignment`, … |
| `app/Actions/` | Business rules, one class per thing a person can do: `CreateUser`, `DeactivateUser`, and later `SubmitTruckCheck`, `ApproveShiftRequest`, … |
| `app/Policies/` | Who's allowed to do what, by role and shift |
| `app/Http/Controllers/` | Screens that are plain forms, such as People |
| `app/Livewire/` | Screens that update as you type *(planned)* |
| `app/Providers/FortifyServiceProvider.php` | How sign-in works: username lookup, the deactivated-account check, rate limits |
| `app/Console/Commands/` | Commands such as `dutyboard:create-admin` for a new install |
| `app/Jobs/` | Background work: texts, backups |
| `app/Notifications/` | Text message wording, in one place |
| `database/migrations/` | Table definitions, in order |
| `database/seeders/` | Made-up sample data for development. Refuses to run in production |
| `resources/views/` | Blade templates for every screen |
| `public/css/app.css` | All styles, with light and dark themes |
| `lang/en/` | Wording for built-in messages, such as sign-in errors |
| `tests/Feature/` | One test file per feature. Test names match acceptance criteria |
| `docs/` | This page, decisions, roadmap, install and admin guides |

## Main tables

| Area | Tables |
|---|---|
| People | `users`: name, username, role, certification level, mobile number, deactivated date, two-step sign-in. *Built.* |
| Places and things | `stations`, `units`. *Built.* |
| Truck checks | `checklist_templates`, `checklist_template_versions`, `truck_checks`, `truck_check_answers` |
| Mileage | `mileage_entries`, `mileage_corrections` |
| Scheduling | `shifts` (A, B, C, with a supervisor), `shift_templates`, `rotation_patterns`, `schedule_months` (draft or published), `assignments` |
| Requests | `coverage_requests`, `shift_requests` (pickups and offers to cover, with approval) |
| Notifications | `calls_for_coverage`, `text_messages` (every text sent and its delivery status) |
| Records | `audit_log` *(built)*, `settings` |

### Rules the schema follows

- **Schedules are rows, not documents.** One `assignments` row per person, per seat, per day: date, station, unit, seat, and user. The rotation pattern generates them. Pickups, coverage, and hours totals all query this one table.
- **History is append-only.** Corrections, template versions, and the audit log only ever add rows. A submitted truck check stores the questions exactly as they were asked, so editing a template never changes past checks.
- **Times are stored in UTC,** and each agency has a time zone setting. Hours are counted from real start and end times. A 24-hour shift that crosses a daylight-saving change is 23 or 25 hours, and the pay-period totals reflect that.
- **Stations exist from the start,** even for an agency with one, so larger departments don't require a migration later.

## Text messages

All texting goes through one interface, `App\Contracts\TextMessageSender`. The first implementation uses Twilio. In development, a log implementation writes messages to `storage/logs/texts.log` instead of sending them. Adding another provider means writing one class. See [decision 0004](decisions/0004-sms-only-one-way.md).

## Data boundaries

DutyBoard must never hold patient information.

- There are no fields for patient names, dates of birth, addresses, incident locations, complaints, or treatment.
- The **run number** is the only link to a call. The call record stays in the agency's patient care reporting system.
- Every free-text field (check notes, correction reasons, request reasons, calls for coverage) shows a reminder that patient information isn't allowed.
- Texts never include patient information.
- Development and tests use made-up data only.

See [decision 0005](decisions/0005-no-patient-data.md).
