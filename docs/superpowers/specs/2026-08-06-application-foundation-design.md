# Phase 2: Application Foundation Design

## Overview
This document outlines the design for the foundation of the TGC Radiology Queueing System's custom PHP backend. The application will use a zero-dependency architecture with a custom autoloader, a bespoke routing system, and a strictly layered Controller-Service-Repository architecture, keeping all SQL execution out of views and controllers.

## Core Application Bootstrapping

### `bootstrap/app.php`
- Initializes the custom autoloader using `spl_autoload_register`.
- Maps the `App\` namespace to the `app/` directory.
- Loads the environment configuration.
- Establishes the singleton PDO database connection using settings from `config/database.php`.

### Custom Router (`App\Core\Router`)
- A lightweight routing component.
- Loads definitions from `routes/web.php`.
- Dispatches HTTP requests to the appropriate Controller method based on the URI and HTTP method.

## Layered Architecture

### Repositories (`App\Repositories\...`)
- **Responsibility**: Direct interaction with the database.
- **Rules**: The only layer allowed to execute SQL queries or interact with PDO.
- **Example**: `ProcedureRepository` will handle fetching records from the `procedures` table.

### Services (`App\Services\...`)
- **Responsibility**: Business logic and rule enforcement.
- **Rules**: Orchestrates data between Repositories and provides processed data to Controllers. Enforces rules like queue priority.
- **Example**: `ProcedureService` will request data from `ProcedureRepository`.

### Controllers (`App\Controllers\...`)
- **Responsibility**: HTTP Request/Response handling.
- **Rules**: Parses incoming requests, calls the appropriate Service methods, and returns JSON or renders views. No direct database access is permitted.
- **Example**: `TestController` handles the incoming browser request and outputs the result.

## Verification Route
A specific route `GET /test-db` will be implemented.
- **Action**: It will hit `TestController@index`.
- **Flow**: `TestController` -> `ProcedureService` -> `ProcedureRepository` -> MySQL `procedures` table.
- **Outcome**: It will output a JSON representation of the procedures seeded in Phase 1, proving the entire vertical slice of the application foundation is functional and connected to the persistent store.
