# MySQL installation and upgrades

Fresh installation: import `init_database.sql` once into an empty database, OR import numbered migrations `01` through `06` in order. Do not combine both installation paths: the seed files insert users.

Existing installation: back up the database, then import only `06_queue_integrity.sql`. It preserves tickets, counters, passwords, and media. Conditional DDL supports MySQL 8 and installations that already added `serving_slot` manually. It adds database room assignments for the five seeded room accounts. General radiology staff and administrators retain access to all rooms. Configure `staff_users.assigned_procedure` (`xray`, `ultrasound`, `ctscan`) and `assigned_slot` (zero-based) together for additional restricted accounts.

The runtime and migration must be deployed together. New internal ticket codes include the date, while patient-facing screens and printed tickets show the short form (for example `XRAY-001`). Old codes are unchanged and still usable for completion. Slot occupancy follows the same current-day scope as the queue display.

Run `php tests/integrity.php` and `php tests/integrity.php --numbered` against a local MySQL server. They create and remove their own randomly named test databases and require CREATE/DROP DATABASE privileges. They never populate the application database. Run `node --test tests/*.test.cjs` for playback regression tests.
