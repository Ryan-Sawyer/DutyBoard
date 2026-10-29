# 0001: Build with Laravel and Livewire

**Status:** Accepted · 2026-10-29

## Situation

DutyBoard needs sign-in with two-step verification, role and shift-based permissions, a database, background jobs for texts, scheduled backups, and pages that work on a phone. It will be maintained by a small team, and we want contributors to need as little special knowledge as possible.

A browser-only app (HTML and JavaScript talking directly to a database) was considered and rejected: every rule would run on the user's device, where anyone can change it, and the database credentials would ship to every phone.

## Decision

Use **Laravel** for the application and **Livewire** for interactive pages. Keep all rules on the server, in one PHP codebase.

## Why

- Laravel includes the security-critical parts we'd otherwise write ourselves: sessions, password hashing, CSRF protection, input escaping, and sign-in rate limiting.
- PHP runs almost anywhere, and many public-sector IT staff already know it.
- Livewire gives pages that update as you type without a separate JavaScript application or a Node build step.
- Laravel's conventions mean a contributor who has done the free Laravel Bootcamp can find their way around.

## What follows

- New Composer packages need an issue and a reason first. See CONTRIBUTING.md.
- No Node toolchain. Styles are plain CSS.
- Laravel releases a major version yearly, with about two years of security fixes. Plan one upgrade a year.
