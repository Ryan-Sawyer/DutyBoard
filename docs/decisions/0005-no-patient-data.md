# 0005: Never store patient information

**Status:** Accepted · 2026-10-29

## Situation

DutyBoard records runs, so it would be easy to add fields that capture patient details. Holding patient information would bring health-privacy obligations the project isn't designed to meet.

## Decision

DutyBoard **never stores patient information**. The run number is the only link to a call. The full call record stays in the agency's patient care reporting system.

## What follows

- No fields for patient names, dates of birth, addresses, incident locations, complaints, or treatment. Pull requests that add them won't be merged.
- Every free-text field carries a visible reminder that patient information isn't allowed.
- Texts never include patient information.
- Seeders, tests, screenshots, and issues use made-up data only.
