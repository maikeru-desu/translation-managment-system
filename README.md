# Translation Management Service API

## Overview

A Laravel-based Translation Management Service API for storing, retrieving, and exporting translations for multiple locales.

## Design Choices

- **Action Pattern**: Business logic is encapsulated in single-purpose action classes, keeping controllers thin
- **Structured API**: Clean RESTful API endpoints for CRUD operations
- **Optimized for Performance**: Efficient querying and caching for handling large datasets
- **Nested JSON Export**: Frontend-ready JSON output using dot notation for nested structures

## Installation

1. Clone the repository
2. Start the Docker environment:
   ```bash
   ./vendor/bin/sail up -d
   ```
3. Install dependencies:
   ```bash
   ./vendor/bin/sail composer install
   ```
4. Run migrations:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```
5. Seed the database (optional):
   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

## API Endpoints

### Authentication
- **POST /api/login**: Get API token
- **POST /api/logout**: Invalidate token

### Locales
- **GET/POST /api/locales**: List/Create locales
- **GET/PUT/DELETE /api/locales/{id}**: Get/Update/Delete locale

### Tags
- **GET/POST /api/tags**: List/Create tags
- **GET/PUT/DELETE /api/tags/{id}**: Get/Update/Delete tag

### Translations
- **GET/POST /api/translations**: List/Create translations
- **GET/PUT/DELETE /api/translations/{id}**: Get/Update/Delete translation
- **GET /api/translations/search**: Search translations
- **GET /api/translations/export**: Export translations as nested JSON

## License

MIT License
