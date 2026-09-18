# TGMCI Radiology Queueing System

## Target Laragon structure

```text
app/
  Controllers/        HTTP request handlers
  Services/           Queue, ticket, report, advertisement, and audit rules
  Repositories/       MySQL data access
  Middleware/         Authentication and role checks
config/               Application and MySQL configuration
database/migrations/  Versioned MySQL schema changes
public/               Laragon web root and browser assets
resources/views/      PHP/HTML presentation templates
routes/               HTTP route definitions
storage/              Logs and uploaded advertisement media
```

The application uses PHP controllers, services, PDO repositories, and MySQL for staff authentication, ticket issuance, queue operations, reports, and advertisement metadata. Advertisement commands, settings, and playback state use JSON files in `storage/`.

## Documentation

The consolidated documentation is in `output/documentation/TGMCI-Radiology-Queueing-System-Documentation.docx` and its PDF companion. It follows the supplied academic reference and includes requirements, diagrams, architecture, database design, interface captures, acceptance test forms, and a staff user manual.

The older root-level TGC Word documents describe an earlier prototype and are retained as historical planning documents. Use the consolidated document for the current implementation review.

## Local deployment

Use PHP 8.1 or newer with PDO MySQL and point the web server document root at `public/`. Configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` through the PHP process environment. The current default database name remains `tgc_radiology_queue` so the project rename does not disconnect an existing installation.

For installation and upgrades, follow `database/migrations/README.md`. New tickets use date-qualified codes (for example `XRAY-20260918-001`). Existing ticket codes remain valid. See `docs/issue-review.md` for the reviewed risks and verification. Playback position publishing requires a staff login in the display browser; anonymous displays continue playing but do not publish position to the dashboard preview.
