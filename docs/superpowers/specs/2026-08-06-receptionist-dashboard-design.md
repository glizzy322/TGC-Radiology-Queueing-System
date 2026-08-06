# Phase 3: Receptionist Dashboard (Ticket Generation) Design

## Overview
This document outlines the design for Phase 3 of the TGC Radiology Queueing System. The goal is to connect the Receptionist Dashboard prototype to the PHP backend, enabling real, database-backed ticket generation using AJAX.

## Backend Architecture

### Repositories
- **`CategoryRepository`**: Fetches patient categories (IPD, OPD) ordered by their priority rank.
- **`TicketRepository`**: Responsible for the transactional creation of a ticket. It will:
  1. Begin a transaction.
  2. Query `queue_counters` with `FOR UPDATE` to lock the row for the specific date and procedure.
  3. If no row exists for today, insert one starting at 1. Otherwise, increment `last_sequence_number`.
  4. Generate the `ticket_code` (e.g., `XR-001`).
  5. Insert the new ticket into `queue_tickets`.
  6. Insert an 'issued' event into `queue_events`.
  7. Commit the transaction.

### Services
- **`TicketService`**: Validates the input (procedure ID, category ID) and delegates the generation logic to the `TicketRepository`.

### Controllers
- **`ReceptionController`**: 
  - Updated to inject `$procedures` (from `ProcedureService`) and `$categories` (from `CategoryRepository`) into the view (`resources/views/receptionist/index.php`).
- **`TicketController`**:
  - Handles `POST /api/tickets`.
  - Parses JSON payload containing `procedure_id` and `category_id`.
  - Calls `TicketService` to generate the ticket.
  - Returns a JSON response containing the newly created ticket data.

## Frontend Modifications (`resources/views/receptionist/index.php`)

### Dynamic UI
- Replace hardcoded Procedure selection buttons with a PHP `foreach ($procedures as $proc)` loop.
- Replace hardcoded Patient Category selection buttons with a PHP `foreach ($categories as $cat)` loop.
- Use `data-procedure-id` and `data-category-id` attributes on the buttons to store the primary keys.

### AJAX Integration
- Modify the click event listener for `#generateTicket`.
- Construct a JSON payload with the selected `procedure_id` and `category_id`.
- Execute a `fetch()` POST request to `/api/tickets`.
- On success, update the `#latestTicket` DOM element with the generated ticket code (e.g., XR-005) and patient category without requiring a page reload.
- (Other dashboard widgets will remain visually static or use existing prototype mock data until Phase 5).
