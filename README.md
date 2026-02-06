# Project & Task Management API

## Tech Stack
- Laravel 12
- MySQL
- Laravel Sanctum
- REST APIs

## Setup Instructions
1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure database credentials
5. Run `php artisan migrate --seed`
6. Start server using `php artisan serve`

## Authentication
This API uses Laravel Sanctum for token-based authentication.
Users receive a Bearer token after login which must be sent in the Authorization header for protected routes.

## API Endpoints

### Authentication
- POST /api/register – Register a new user
- POST /api/login – Login and receive authentication token
- POST /api/logout – Logout user (authenticated)

### User
- GET /api/profile – Get authenticated user profile

### Tasks
- GET /api/tasks – List all tasks (paginated)
- POST /api/tasks – Create a new task
- GET /api/tasks/{id} – Get task details
- PUT /api/tasks/{id} – Update a task
- DELETE /api/tasks/{id} – Delete a task

### Task Dependencies
- POST /api/task-dependency – Add dependency between tasks
- DELETE /api/task-dependency/{id} – Remove task dependency


## Task Dependency Handling
- Tasks can depend on other tasks
- Circular dependencies are prevented
- A task cannot be completed if its dependent tasks are incomplete

## Authorization
- Users can access only their own tasks
- Ownership checks are enforced in controllers
- All protected routes require authentication

## Security Measures
- Input validation
- Rate limiting
- Proper HTTP status codes

## Performance
- Pagination
- Eager loading
- Indexed dependency tables

## Assumptions
- Tasks belong to a single user
- Dependencies exist only between tasks of the same user
