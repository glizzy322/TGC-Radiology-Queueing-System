# Phase 1: MySQL Schema Design

## Overview
This document outlines the database schema design for Phase 1 of the TGC Radiology Queueing System. The system will be locally deployed using Laragon (PHP/MySQL) and requires a robust relational database to handle queueing rules, tickets, user roles, and audit trails.

## Core Configuration & Users

### `users`
Stores staff accounts and credentials.
- `id`: Primary key
- `name`: Full name of the staff member
- `role`: Enum ('receptionist', 'radiology_staff', 'administrator')
- `password`: Hashed password
- `created_at`, `updated_at`: Timestamps

### `procedures`
Stores available radiology procedures.
- `id`: Primary key
- `code`: e.g., 'XRAY', 'US', 'CT'
- `name`: e.g., 'X-Ray', 'Ultrasound', 'CT Scan'
- `created_at`, `updated_at`: Timestamps

### `patient_categories`
Stores patient types and their priority.
- `id`: Primary key
- `code`: 'IPD', 'OPD'
- `priority_rank`: Integer (Lower number = higher priority. e.g., IPD = 1, OPD = 2)
- `created_at`, `updated_at`: Timestamps

## Queue & Tracking Logic

### `daily_counters`
Tracks sequence numbers for ticket generation.
- `id`: Primary key
- `date`: Current calendar date
- `procedure_id`: Foreign key to `procedures`
- `last_sequence_number`: Integer for the last generated ticket

### `tickets`
The main queue tracking table.
- `id`: Primary key
- `ticket_code`: Formatted code (e.g., 'XRAY-001')
- `procedure_id`: Foreign key
- `category_id`: Foreign key
- `status`: Enum ('waiting', 'serving', 'completed', 'skipped', 'cancelled')
- `issue_time`: Timestamp
- `serving_time`: Timestamp (nullable)
- `completed_time`: Timestamp (nullable)
- `issuer_id`: Foreign key to `users`
- `created_at`, `updated_at`: Timestamps

### `queue_events`
Audit trail for every status change on a ticket.
- `id`: Primary key
- `ticket_id`: Foreign key
- `actor_id`: Foreign key to `users`
- `action`: String description (e.g., 'created', 'called', 'completed')
- `created_at`: Timestamp

### `advertisements`
Stores metadata for the public display ads.
- `id`: Primary key
- `filepath`: Path to the media file
- `duration_seconds`: Display time per rotation
- `is_active`: Boolean
- `display_order`: Integer
- `created_at`, `updated_at`: Timestamps

## Next Steps
Once this design is approved, an implementation plan will be created to generate the Laravel-style migrations and seeders for these tables.
