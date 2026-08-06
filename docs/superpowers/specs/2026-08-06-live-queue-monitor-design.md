# Phase 5: Live Queue Monitor & Dashboard Design

## Overview
Phase 5 integrates the frontend Dashboard Analytics and the Public Display ("Now Serving" screen) with the real database, replacing the temporary LocalStorage data structure. It implements a JSON polling architecture to keep screens synced in near real-time.

## Backend API Architecture

### `QueueRepository`
A repository responsible for aggregating queue states from `queue_tickets`, `procedures`, and `patient_categories`.
- `getTodayState()`: Fetches all tickets generated today, separated into `waiting`, `serving`, and `completed` arrays grouped by procedure key.
- `callNext(procedureKey)`: Locates the oldest `waiting` ticket for a procedure, updates its status to `serving`, sets `serving_time`, inserts a `queue_events` audit record, and returns it.
- `completeTicket(ticketId)`: Updates a specific ticket's status to `completed`, sets `completed_time`, and inserts an audit record.

### `QueueController`
Provides secure API endpoints for the frontend to consume.
- `GET /api/queue`: Returns the full state payload (`queues`, `serving`, `completed`).
- `POST /api/queue/call`: Authorized endpoint (Radiology Staff only) to trigger `callNext`.
- `POST /api/queue/complete`: Authorized endpoint (Radiology Staff only) to trigger `completeTicket`.

## Frontend Architecture

### Dashboard Integration (`resources/views/dashboard/index.php`)
- **API Polling**: A `setInterval` loop will fetch `/api/queue` every 2 seconds.
- **Analytics Sync**: The returned data will be fed into `renderDashboard()` to update the "Total tickets", "Busiest category", and chart visuals using real DB data.
- **Manage Queue Actions**: The "Call Next" and "Complete" buttons in the radiology view will execute `fetch()` POST requests to the new API endpoints instead of mutating local state directly.

### Public Display Integration (`resources/views/partials/public-display-content.php`)
- **API Polling**: Replaces `localStorage.getItem('radiologyQueueState')` with a 2-second `fetch('/api/queue')` loop.
- **Data Transformation**: The API response will be mapped to the expected `state.queues` and `state.serving` formats so the existing rendering logic (`renderIncoming` and `renderServing`) works seamlessly.
- **Voice Announcements**: The TTS (Text-to-Speech) logic will remain. It will detect diffs in the `serving` arrays returned by the API to trigger the "Queue number XR-001, please proceed to X-Ray" announcement.
