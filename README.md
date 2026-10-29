# DutyBoard

Truck checks, run mileage, and shift scheduling for fire and EMS agencies.

DutyBoard is a web app that crews use from a phone in the truck bay and supervisors use from a desk. It runs as a set of containers on a server your agency controls. It is open source under the [Apache 2.0 license](LICENSE).

> **Status:** early development. The first release is being built for Atoka County EMS. Nothing here is ready for production use yet. See the [roadmap](docs/ROADMAP.md).

## What it does

- **Truck checks:** editable checklists answered as Pass / Fail / N/A, a number, or text. Every Fail needs a note, and supervisors review deficiencies.
- **Run mileage:** start and end odometer per run, with warnings for readings that don't add up. Corrections keep the original value and the reason.
- **Shift scheduling:** monthly schedules built from shift templates and rotation patterns (for example, 48/96), with staffing warnings, draft and published states, and shift pickup and coverage requests that a supervisor approves.
- **Text notifications:** one-way SMS for calls for coverage, approvals, and published schedules.
- **Audit trail:** every create, submit, correct, approve, and delete is recorded with who and when.

DutyBoard is **not** a patient care record. It never stores patient information. See [Data boundaries](docs/ARCHITECTURE.md#data-boundaries).

## Try it locally

You need [Docker](https://docs.docker.com/get-docker/) and git. Nothing else.

```bash
git clone https://github.com/Ryan-Sawyer/DutyBoard.git
cd DutyBoard
cp .env.example .env
docker compose up
```

Then open http://localhost:8080. The app starts with made-up sample data: a small agency with three units, a 48/96 rotation, and staff you can sign in as. Texts are written to a log file instead of being sent.

> These commands will work once the first slice lands. Until then, the repo holds the docs that describe how it will be built.

## Documentation

| If you want to… | Read |
|---|---|
| Understand how the code is organized | [Architecture](docs/ARCHITECTURE.md) |
| Know why something was built the way it was | [Decision records](docs/decisions/) |
| See what's being built, and in what order | [Roadmap](docs/ROADMAP.md) |
| Contribute code, docs, or bug reports | [Contributing](CONTRIBUTING.md) |
| Report a security problem | [Security policy](SECURITY.md) |

## License

Apache License 2.0. See [LICENSE](LICENSE).
