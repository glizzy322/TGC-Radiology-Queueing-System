# TGC Radiology Queueing System

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

The existing pages are still a browser-storage prototype. The next implementation step is to replace `localStorage` and IndexedDB reads/writes with PHP services and MySQL repositories.
