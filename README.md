# Rules-Based Customer Support Chatbot API

A RESTful customer-support chatbot API built with Laravel 12. It provides JWT authentication, conversation management, persistent message history, configurable keyword-based automatic replies, administrator-managed reply rules, and WebSocket broadcasting for completed bot messages.

**Note:** This project currently uses a rules-based reply engine. It does not integrate with an AI or large language model (LLM) provider.

## Overview

The API allows users to register, authenticate, create conversations, send messages, retrieve conversation history, and close conversations.

When a user sends a message, the application stores it, checks the configured automatic-reply rules, and stores the matching bot response. A `BotMessageCreated` event is dispatched to support real-time message delivery.

Administrators can manage automatic-reply rules and configure the fallback response used when no keyword matches.

## Features

- JWT authentication for protected API endpoints
- User and administrator roles
- Conversation creation, listing, retrieval, and closing
- Conversation ownership authorization
- Persistent user and bot messages
- Transactional message processing
- Keyword-based automatic replies
- Configurable fallback responses
- Administrator-only reply rule management
- Private conversation channel authorization
- WebSocket broadcasting of completed bot messages
- Form Request validation
- Centralized JSON exception responses
- Security headers and route-level rate limiting

## Technology Stack

| Category | Technology |
|---|---|
| Backend framework | Laravel 12 |
| Programming language | PHP 8.2+ |
| Authentication | JWT |
| Database | PostgreSQL configuration |
| Frontend asset tooling | Vite, Node.js, npm |
| Real-time communication | Laravel Broadcasting / Reverb-compatible setup |
| Testing framework | Pest 3, PHPUnit |
| Code formatting | Laravel Pint |

Laravel Sanctum is installed but is not used by the current JWT authentication flow.

## Architecture

The application separates HTTP handling, validation, business logic, and response formatting.

- **Controllers:** Handle incoming requests and coordinate application services.
- **Form Requests:** Validate incoming data.
- **Services:** Implement message processing and automatic-reply matching.
- **Models:** Represent users, conversations, messages, and reply rules.
- **API Resources:** Format API responses.
- **Events:** Broadcast completed bot messages.

### Message Flow

```text
Client
  |
  | POST /api/conversations/{id}/messages
  v
MessageController
  |
  v
MessageService::send()
  |
  |-- Store USER message
  |-- Find matching automatic-reply rule
  |-- Store BOT response
  |-- Dispatch BotMessageCreated event
  |
  v
Database and private conversation channel
```

Message processing runs inside a database transaction. The reply engine normalizes the message and configured keywords, then searches active rules for a keyword contained in the message. If no keyword matches, the configured fallback rule is used.

## API Endpoints

All endpoints use the `/api` prefix. Protected endpoints require a JWT bearer token unless otherwise specified.

### Authentication

| Method | Endpoint | Access | Description |
|---|---|---|---|
| POST | `/api/auth/register` | Public | Register a user |
| POST | `/api/auth/login` | Public | Authenticate and receive a JWT |
| POST | `/api/auth/logout` | Authenticated | Log out |

Registration currently returns a user resource without issuing a token. Login returns the JWT and user information.

### Conversations and Messages

| Method | Endpoint | Access | Description |
|---|---|---|---|
| POST | `/api/conversations` | Authenticated | Create a conversation |
| GET | `/api/conversations` | Authenticated | List the current user's conversations |
| GET | `/api/conversations/{id}` | Conversation owner | Retrieve a conversation |
| PATCH | `/api/conversations/{id}/close` | Conversation owner | Close a conversation |
| GET | `/api/conversations/{id}/messages` | Conversation owner | Retrieve message history |
| POST | `/api/conversations/{id}/messages` | Conversation owner | Send a message and receive a bot reply |

### Administrator: Automatic-Reply Rules

These endpoints require authentication and administrator authorization.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/auto-replies` | List automatic-reply rules |
| POST | `/api/admin/auto-replies` | Create a rule |
| GET | `/api/admin/auto-replies/{id}` | Retrieve a rule |
| PUT | `/api/admin/auto-replies/{id}` | Update a rule |
| DELETE | `/api/admin/auto-replies/{id}` | Delete a rule |
| PATCH | `/api/admin/auto-replies/{id}/toggle` | Toggle a rule's active state |
| GET | `/api/admin/auto-replies/fallback` | Retrieve the fallback rule |
| PUT | `/api/admin/auto-replies/fallback` | Update the fallback rule |

## Example Request

Send a message to an existing conversation:

```http
POST /api/conversations/1/messages
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
Accept: application/json
```

```json
{
  "content": "Hello"
}
```

The API returns the stored user message and the generated bot message. The response text depends on the active rules configured in the database.

Example response structure:

```json
{
  "success": true,
  "message": "Message sent successfully.",
  "data": {
    "user_message": {
      "id": 1,
      "sender_type": "USER",
      "content": "Hello"
    },
    "bot_message": {
      "id": 2,
      "sender_type": "BOT",
      "content": "A configured automatic response"
    }
  }
}
```

This is an illustrative example. Check the current API Resources to confirm the exact response fields.

Errors use a centralized JSON structure:

```json
{
  "success": false,
  "message": "A descriptive error message.",
  "errors": {}
}
```

Relevant HTTP status codes include `401`, `403`, `404`, `422`, `429`, and `500`.

## Data Model

The application has four main entities:

- **Users:** User identity, email, password hash, and role.
- **Conversations:** Conversation owner and `OPEN` or `CLOSED` status.
- **Messages:** Conversation reference, sender type, and message content.
- **Automatic-Reply Rules:** Keyword arrays, response text, and active status.

A user can own multiple conversations, and each conversation can contain multiple messages. Automatic-reply rules are managed separately.

## Getting Started

### Prerequisites

- PHP 8.2 or later
- Composer 2
- Node.js and npm
- PostgreSQL, or another database configured for the application
- Optional: Laravel Reverb or a compatible broadcasting setup for real-time events

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
cd YOUR_REPOSITORY
```

Replace the URL and directory with your actual repository details.

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment Variables

Create your local environment file:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Configure the database connection in `.env`.

The application also requires JWT configuration. Generate the JWT secret using the command supported by the installed JWT package, commonly:

```bash
php artisan jwt:secret
```

If you use Reverb, configure the required `REVERB_*` variables and any necessary `VITE_REVERB_*` client variables.

**Important:** The current `.env.example` is missing some JWT and Reverb-related variables. Complete and verify the example environment file before expecting a fresh installation to work without additional configuration.

Never commit `.env` or real credentials to version control.

### 4. Run Database Migrations and Seeders

After configuring a working database, run:

```bash
php artisan migrate --seed
```

Review the seeders before running them. Development accounts and hardcoded example passwords must not be used in a public or production environment.

### 5. Build Frontend Assets

```bash
npm run build
```

### 6. Start the Application

Start the Laravel development server:

```bash
php artisan serve
```

For frontend asset development, use another terminal:

```bash
npm run dev
```

If real-time broadcasting is configured, start the broadcasting server separately when required:

```bash
php artisan reverb:start
```

The exact broadcasting setup depends on your environment. This repository does not include a dedicated chat frontend.

## Testing and Code Quality

Run the test suite:

```bash
php artisan test
```

Format PHP files with Laravel Pint:

```bash
vendor/bin/pint
```

### Current Test Status

The reviewed repository contains only generated example tests. Dedicated feature tests for authentication, conversation authorization, message processing, automatic-reply rules, and administrator endpoints have not yet been implemented.

The existing feature example expects `GET /` to return HTTP `200`, but the reviewed web routes do not define that route. This is a potential failing test and should be verified by running the test suite.

Test results have not been confirmed as passing.

## Security Considerations

- Protected API endpoints use JWT authentication.
- Conversation access is scoped to the authenticated owner.
- Automatic-reply rule mutations are restricted to administrators.
- Form Requests provide input validation.
- Route-level rate limiting and security headers are configured.
- Never commit `.env`, JWT secrets, application keys, or database credentials.
- Remove hardcoded development passwords and use secure configuration for administrator accounts.
- Remove or strictly restrict diagnostic routes that render user-controlled content as raw HTML. In particular, review the `/xss-test` route before making the repository public.
- Configure HTTPS, production error handling, allowed origins, and broadcasting credentials before deployment.

## Current Limitations

- The bot uses deterministic keyword matching rather than an AI/LLM provider.
- Messages are processed independently; conversation context and memory are not implemented.
- Broadcasting delivers completed messages rather than token-by-token streaming.
- Message history does not currently have pagination and is returned newest-first.
- No dedicated chat frontend is included.
- File and media uploads are not implemented.
- Email notifications are not implemented.
- OpenAPI/Swagger documentation and a Postman collection are not included.
- Automated test coverage is limited.
- Environment examples and fresh-install instructions need further verification.

## Roadmap

- Add feature tests for authentication, authorization, conversations, messages, and administrator rule management.
- Fix or replace the default root-route example test.
- Add OpenAPI documentation or a maintained Postman collection.
- Complete and verify `.env.example`.
- Remove or secure diagnostic routes and parameterize development credentials.
- Add message pagination and consider chronological ordering for chat clients.
- Remove unused configuration and dependencies where appropriate.
- Add continuous integration for tests and code formatting.
- If needed, introduce an AI reply service behind a clear interface while retaining the rules engine as a fallback.
- Build or integrate a dedicated chat frontend.

## License

The project metadata declares MIT, but a root `LICENSE` file was not present in the reviewed repository. Add the appropriate license file before presenting the repository as formally licensed.

---

## Project Status

This README documents the code as reviewed. It does not claim that all setup steps, tests, or broadcasting features have been successfully executed. Verify the installation process, test suite, seed data, and real-time configuration before describing the project as production-ready.
