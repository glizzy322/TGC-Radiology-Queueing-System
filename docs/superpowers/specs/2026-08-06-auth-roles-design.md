# Phase 4: Authentication & Role-Based Access Design

## Overview
Phase 4 implements staff authentication and secures the dashboard based on the logged-in user's role. It introduces session management and dynamically hides/shows UI elements so Receptionists can only generate tickets, and Radiology Staff can only call/complete them.

## Backend Architecture

### Security Core
- **Database Seed**: `05_seed_staff.sql` will create two users: `receptionist` and `radiology` using `password_hash()`.
- **`UserRepository`**: Handles querying the `staff_users` table by username.
- **`AuthService`**: Contains the business logic to authenticate credentials (`password_verify`) and initiate PHP sessions.
- **`AuthController`**: Provides routes for `GET /login` (view), `POST /login` (submit), and `POST /logout` (destroy session).

### Route Protection
- **Session Checking**: In `bootstrap/app.php` (or a base controller middleware pattern), we will enforce session start.
- **`DashboardController`** (formerly `ReceptionController`): Checks if `$_SESSION['user_id']` exists. If not, redirects to `/login`. Passes `$_SESSION['user_role']` to the view.
- **`TicketController`**: Checks if the user is authenticated *and* has the `receptionist` role before generating a ticket. If unauthorized, returns a 403 JSON response.

## Frontend Modifications

### Views
- **`resources/views/auth/login.php`**: A new login page styled to match the premium aesthetics of the application.
- **`resources/views/dashboard/index.php`** (formerly `receptionist/index.php`):
  - Will receive `$userRole` from the controller.
  - **Navigation Sidebar**: 
    - If `$userRole === 'receptionist'`, render the "Reception" nav button.
    - If `$userRole === 'radiology_staff'`, render the "Manage queue" nav button.
  - **Main Content Views**:
    - Conditionally echo the `<section id="receptionView">` and `<section id="manageView">` based on the role.
  - **Account Panel**: Add a dynamic display of the logged-in user's name and a functional "Sign Out" button hitting `/logout`.
