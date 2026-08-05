# MySQL migrations

Place versioned MySQL migration files in this folder when persistent storage is implemented.

The planned migration order is:

1. `staff_users`, `procedures`, and `patient_categories`
2. `queue_counters`, `queue_tickets`, and `queue_events`
3. `advertisements` and `audit_logs`

Use InnoDB, `utf8mb4`, foreign keys, and transactions for ticket creation and status changes. Set the IPD priority rank ahead of OPD.
