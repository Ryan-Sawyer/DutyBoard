# Contributing to DutyBoard

Thanks for helping. DutyBoard is built so that someone who knows a little PHP, or works on an ambulance and is curious about code, can find their way around and make a change. If anything in this guide or the code is confusing, that's a bug in the docs. Open an issue and say so.

## Ways to help

- **Report a bug or a confusing screen.** Open an issue using the bug template. Screenshots help.
- **Suggest a feature.** Open an issue using the feature template, and describe the problem from the station's side first ("supervisors can't tell who's already worked 48 hours"), then the idea.
- **Improve the docs.** Typos, unclear steps, and missing explanations are all welcome pull requests.
- **Write code.** Issues labeled **good first issue** are small and well described. Comment on one to claim it.

Never put real patient information, staff phone numbers, or anything from a real agency's records in an issue, a pull request, or test data.

## Set up a development copy

You need Docker and git. You don't need PHP, Composer, Node, or a database installed on your machine. Everything runs in containers.

```bash
git clone https://github.com/Ryan-Sawyer/DutyBoard.git
cd DutyBoard
cp .env.example .env
docker compose run --rm app php artisan key:generate --show   # paste the result into APP_KEY in .env
docker compose up -d
docker compose exec app php artisan db:seed
```

The app container installs PHP packages and applies database changes each time it starts. Open http://localhost:8080 and sign in as one of the sample users in `database/seeders/SampleAgencySeeder.php`. Every sample password is `dutyboard`.

On Linux, if your user ID isn't 1000, set `DUTYBOARD_UID` and `DUTYBOARD_GID` in `.env` (see `.env.example`).

To start over with fresh sample data:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Run any Laravel command inside the app container:

```bash
docker compose exec app php artisan <command>
```

## Run the tests

```bash
docker compose exec app php artisan test
```

Tests run against PostgreSQL, the same as production, in their own `dutyboard_test` database. The postgres container creates it the first time it starts, so running tests never touches your sample data.

Every acceptance criterion in the scope has a test named after it, for example `test_a5_fail_without_note_cannot_be_submitted`. If you change behavior, update or add the matching test. A pull request with failing tests won't be merged.

## How the code is laid out

Read [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) first. It's one page. The short version:

- DutyBoard follows **standard Laravel conventions**. If you've done the free [Laravel Bootcamp](https://bootcamp.laravel.com), you already know where things live.
- Code is named after what **stations call things**: `TruckCheck`, `Deficiency`, `MileageEntry`, `ShiftRequest`, `CallForCoverage`.
- Each business rule lives in **one place**, with a comment explaining why it exists.

## Style

- **Clear beats clever.** Write code that the next person can read without asking you. Prefer a few plain lines over a dense one-liner.
- **Comment the why, not the what.** `// A Fail needs a note so the supervisor knows what to fix` is useful. `// check if note is empty` is not.
- **Formatting** is handled by Laravel Pint. Run `docker compose exec app ./vendor/bin/pint` before committing.
- **Dependencies:** don't add a Composer package without opening an issue first. Each one is something the project has to keep patched. There is no Node toolchain, and we'd like to keep it that way.
- **Screens must work on a phone.** Crews use DutyBoard from the truck bay. Check any screen you change at phone width.
- **Words on screen:** use plain language and the terms crews use. Buttons say what they do ("Submit check", not "OK").

## Making a change

1. Fork the repo and create a branch named for the change, for example `fix-mileage-warning` or `docs-install-steps`.
2. Make the change, with tests.
3. Run the tests and Pint.
4. Open a pull request. Describe what changed and why, and link the issue it closes. Add a screenshot for anything visible.
5. A maintainer will review it. Expect questions; they're how the code stays readable.

## Decisions that are already made

Some choices have been discussed and settled, such as PostgreSQL only, no Node toolchain, and one-way texts. They're written up in [docs/decisions/](docs/decisions/), with the reasons. If you think one should change, open an issue that addresses the reasons in the record.

## Code of conduct

Be respectful and assume good intent. Many contributors work in public safety and contribute on their own time, between shifts. Harassment or personal attacks aren't tolerated, and maintainers will remove comments or contributors that cross that line.
