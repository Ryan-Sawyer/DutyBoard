# Decision records

Short notes on choices that shaped DutyBoard, and why they were made. They save contributors from re-arguing settled questions, and they tell future maintainers what to reconsider if circumstances change.

| # | Decision | Status |
|---|---|---|
| [0001](0001-laravel-and-livewire.md) | Build with Laravel and Livewire | Accepted |
| [0002](0002-postgresql-only.md) | Support PostgreSQL only | Accepted |
| [0003](0003-one-install-per-agency.md) | One install per agency, on the agency's server | Accepted |
| [0004](0004-sms-only-one-way.md) | Notifications by one-way SMS only | Accepted |
| [0005](0005-no-patient-data.md) | Never store patient information | Accepted |

## Adding one

Copy the format of an existing record, give it the next number, and open a pull request. A record is short: the situation, the decision, and what follows from it. To change a decision, add a new record that replaces the old one, and mark the old one **Superseded by 00XX**. Don't edit history.
