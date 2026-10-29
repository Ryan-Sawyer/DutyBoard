# Roadmap

DutyBoard is built in thin slices. Each slice works end to end: screens, rules, tests, docs, and the container setup. Later slices copy the patterns the first one sets.

The acceptance criteria (A1–A23) come from the MVP scope written for Atoka County EMS.

## Slice 1: Foundation and truck checks

The first slice sets the structure everything else follows.

- [x] Laravel project and development containers (app, worker, PostgreSQL), `.env.example`
- [ ] Production containers: PHP-FPM behind Caddy with HTTPS
- [x] Sample data seeder: one station, three units, a dozen made-up staff (rotation comes with scheduling)
- [x] Sign-in, two-step sign-in, and roles: Crew, Supervisor, Administrator (A1, A2)
- [x] Administrator manages people, including deactivating them and resetting passwords (A1)
- [ ] Administrator manages units, including taking them out of service (A3)
- [ ] Checklist templates with versions (A4)
- [ ] Crew completes and submits a truck check from a phone. A Fail needs a note (A5)
- [ ] Supervisor reviews deficiencies with a note (A6)
- [x] Audit log, append-only (A12, for what's built so far)
- [ ] Runs from the published image and compose file over HTTPS (A13)
- [x] CI: tests on PostgreSQL, Pint, `composer audit`, and a container start-up check on every pull request
- [ ] Docs: install guide, admin guide for this slice, architecture updated

## Slice 2: Run mileage and reports

- [ ] Mileage entry with calculated miles and odometer warnings (A7)
- [ ] Supervisor corrections that keep the original value and reason (A8)
- [ ] Mileage and truck check reports with CSV export (A11)
- [ ] Automated daily backups and a tested restore procedure (A14)

## Slice 3: Scheduling

- [ ] Shifts, shift supervisors, shift templates, and rotation patterns
- [ ] Monthly schedule with staffing warnings, draft and published states, and copy forward (A9)
- [ ] Month, day, and "my shifts" views. Print and PDF (A10)
- [ ] Settings: time zone, pay period start and length, consecutive-hours limit

## Slice 4: Pickup, coverage, and approvals

- [ ] Pickup requests and offers to cover (A17)
- [ ] Routing to the shift supervisor and administrators (A18)
- [ ] Approval cards with the consecutive-hours flag and pay-period hours (A19)
- [ ] Coverage requests for your own shifts (A20)

## Slice 5: Text messages

- [ ] Mobile numbers, text consent, and STOP handling (A22, A23)
- [ ] Twilio sender, plus the development log sender
- [ ] Texts for each notification event, and password reset by texted code (A22, A23)
- [ ] Calls for coverage (A21)
- [ ] Install guide section on carrier (A2P 10DLC) registration

## Release

- [ ] Documentation complete: install, admin, text setup, backup and restore (A15)
- [ ] Repository has no passwords, keys, phone numbers, or agency data (A16)

## Later

Not in the first release, but the design leaves room for them: time-off requests, direct shift swaps, importing history from other systems, and integrations with CAD or ePCR systems.
