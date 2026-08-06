# Phase 6: Reports and Advertisements Design

## Overview
Phase 6 replaces browser-based storage (LocalStorage and IndexedDB) for Analytics and Advertisements with a real backend API, using MySQL for data and local server storage for media files.

## Backend Architecture

### Reports API
- **`ReportController`**: Handles analytic requests from the dashboard.
- **`GET /api/reports/tickets`**: Accepts `start_date` and `end_date` parameters. Securely queries `queue_tickets` (joining `procedures` and `patient_categories`) to return all generated and completed tickets in that range. Used to power the frontend charts and the SheetJS Excel export.

### Advertisements API
- **`AdvertisementRepository`**: Manages MySQL interactions for the `advertisements` table.
- **`AdvertisementController`**: Handles ad media uploads and management. Requires `receptionist` or `radiology_staff` role.
- **`GET /api/ads`**: Returns all ads ordered by `display_order`.
- **`POST /api/ads`**: Handles `multipart/form-data` uploads. Validates file type (images/videos) and size. Moves the file to `public/storage/uploads/ads/` and inserts the metadata (path, duration, active status) into the database.
- **`DELETE /api/ads/{id}`**: Deletes the database record and unlinks the physical file.

## Frontend Architecture

### Dashboard Reporting
- Replace the in-memory array filtering in `dashboard/index.php`.
- When a date shortcut ("This week", "This month") is clicked, or dates are manually entered, `fetch('/api/reports/tickets?start=...&end=...')` is called.
- The returned JSON replaces `state.generatedTickets` and `state.completed`, immediately triggering chart re-renders.
- The "Download Excel" button continues to use the existing SheetJS logic, now perfectly powered by the historical data.

### Dashboard Advertisements
- Remove IndexedDB (`ADS_DB_NAME`) completely.
- Update the Ad upload form (`#adForm`) to construct a `FormData` object containing the file, duration, and active status.
- POST the `FormData` to `/api/ads`.
- Provide a UI function to delete ads by making a `DELETE` request.

### Public Display
- Remove IndexedDB loading in `public-display-content.php`.
- Poll `GET /api/ads` periodically (e.g., every 15-30 seconds).
- Loop through the returned ads and display them using the exact same image/video logic, pointing `src` to the real server paths.
