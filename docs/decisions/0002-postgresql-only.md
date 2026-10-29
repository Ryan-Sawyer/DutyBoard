# 0002: Support PostgreSQL only

**Status:** Accepted · 2026-10-29

## Situation

DutyBoard has to work for a 10-person county EMS agency and for a fire department with hundreds of staff and many stations. SQLite would be simpler for the small agency, and Laravel can run on several databases.

## Decision

Support **PostgreSQL only**, in development, tests, and production.

## Why

- SQLite allows one write at a time, which breaks down when a large department's crews submit truck checks at shift change.
- PostgreSQL can be backed up while it's running (`pg_dump`), which the automatic daily backup depends on.
- Its date, time-zone, and JSON handling suit scheduling and checklist answers.
- Supporting two databases doubles the testing and invites bugs that appear on only one of them.

## What follows

- PostgreSQL runs as one container in the compose file, so a small agency doesn't have to manage it separately.
- Code may use PostgreSQL-specific features where they make the code clearer.
