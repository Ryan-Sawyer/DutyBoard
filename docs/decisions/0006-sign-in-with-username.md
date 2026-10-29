# 0006: Sign in with a username, not an email address

**Status:** Accepted · 2026-10-29

## Situation

Most web apps sign people in with an email address, and Laravel's defaults assume it. Many EMS and fire crew members don't have an agency email address, and DutyBoard doesn't send email (decision 0004), so an email address would be one more thing to collect that the app never uses.

## Decision

People sign in with a **username** chosen by the administrator who creates their account, such as `jalvarez`. Usernames are stored in lowercase, so `JAlvarez` and `jalvarez` are the same person.

## What follows

- There's no "forgot password" email. An administrator sets a new password from the People screen. Resetting by texted code comes with text notifications.
- An administrator can turn off someone's two-step sign-in if they lose their phone and their recovery codes.
- Laravel's `password_reset_tokens` table still exists, because Laravel clears it whenever a password changes. It normally stays empty.
