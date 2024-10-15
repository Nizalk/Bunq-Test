# Chat Application Backend

This project is a backend solution for a chat application, written as part of a backend engineering coding assignment. The application is built in PHP using the Slim framework, with a focus on clean, readable, and secure code that meets high standards.

## Table of Contents
- [Assignment Overview](#assignment-overview)
- [Tech Stack](#tech-stack)
- [Features](#features)
- [Setup and Installation](#setup-and-installation)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Notes and Considerations](#notes-and-considerations)

---

## Assignment Overview

The goal of this project is to build a backend for a chat application where users can:
- Create and join public chat groups.
- Send messages within these groups.
- Retrieve a list of messages from any group.

The project requirements include:
- A RESTful JSON API over HTTP(s) for client-server communication.
- Secure and well-structured code with separation of concerns.
- Use of an SQLite database for data persistence.
- Unit and integration tests for critical components.

## Tech Stack

- **PHP**: Core programming language.
- **Slim Framework**: Lightweight PHP framework used for routing and handling requests.
- **SQLite**: Database for persistent storage.
- **JWT (JSON Web Tokens)**: Used for user identification and authentication.
- **PHPUnit**: Testing framework for unit and integration tests.

## Features

- **User Management**: Users can join chat groups and are identified by a unique token.
- **Group Management**: Users can create and join public chat groups.
- **Messaging**: Users can send messages within groups and retrieve messages from any group.
- **Secure API**: Uses JWT for user identification and authentication.
- **Testing**: Comprehensive unit and integration tests ensure code reliability.

## Setup and Installation

### Prerequisites
- PHP 8.0 or higher
- Composer (PHP dependency manager)

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/chat-backend.git
   cd chat-backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up the environment:
   - Copy `.env.example` to `.env` and configure your settings (e.g., `JWT_SECRET` for token generation).
   ```bash
   cp .env.example .env
   ```
   
4. Set up the SQLite database:
   ```bash
   touch database/chat.db
   php migrate.php  # Run database migrations
   ```

5. Start the application (for testing locally):
   ```bash
   php -S localhost:8000 -t public
   ```

The application will now be accessible at `http://localhost:8000`.

## API Endpoints

| Method | Endpoint           | Description                       |
| ------ | ------------------- | --------------------------------- |
| POST   | `/groups`           | Create a new chat group           |
| POST   | `/groups/{id}/join` | Join a chat group                |
| POST   | `/groups/{id}/messages` | Send a message to a group  |
| GET    | `/groups/{id}/messages` | Get all messages in a group |

### Example Requests

**Create a Group**
```http
POST /groups
Content-Type: application/json

{
    "name": "Group Name"
}
```

**Join a Group**
```http
POST /groups/{id}/join
Content-Type: application/json

{
    "username": "User123"
}
```

**Send a Message**
```http
POST /groups/{id}/messages
Content-Type: application/json

{
    "username": "User123",
    "message": "Hello, World!"
}
```

**Retrieve Messages**
```http
GET /groups/{id}/messages
Content-Type: application/json
```

## Testing

The project includes unit and integration tests to verify functionality. To run tests, use:

```bash
vendor/bin/phpunit
```

### Test Coverage

- **User and Group Management**: Tests for user and group creation, joining groups.
- **Messaging**: Tests for message sending and retrieval.
- **Authentication**: Tests for token generation and validation.

## Project Structure

```plaintext
├── config
│   └── database.php         # Database connection configuration
├── database
│   └── chat.db              # SQLite database file
├── public
│   └── index.php            # Entry point for the application
├── src
│   ├── Models               # Contains Eloquent models for User, Group, etc.
│   ├── Middleware           # JWT and other middleware
│   └── dependencies.php     # Dependency injection setup
├── tests                    # PHPUnit test cases
│   └── UserTest.php         # Tests for user-related functionalities
├── .env.example             # Environment variables example
└── composer.json            # Composer dependencies and autoload config
```

## Notes and Considerations

- **Code Quality**: Emphasis on clean, readable, and secure code with separation of concerns.
- **Environment Configuration**: Use `.env` to configure sensitive data like JWT secrets.
- **Security**: JWT is used for user identification; ensure the `JWT_SECRET` is kept secure.
- **Performance**: Consider optimizing for scale if deploying in a high-traffic environment.


