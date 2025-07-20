# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a full-stack accommodation booking platform called "PopnBed" built with a Symfony backend and Nuxt.js frontend. The application allows users to search for themed accommodations, make bookings, and manage their listings.

## Development Environment

### Starting the Application
```bash
# Development
docker-compose up --build -d

# Production
docker compose -f compose.prod.yaml up -d
```

### Available Services
- **Frontend**: http://localhost:3000 (Nuxt.js)
- **Backend**: http://localhost:8000 (Symfony)  
- **Database**: MySQL on port 3306
- **PhpMyAdmin**: http://localhost:8080
- **Mercure Hub**: http://localhost:1337 (real-time messaging)
- **Nginx**: http://localhost:8085
- **Matomo**: http://localhost:8082 (analytics)

## Frontend (Nuxt.js)

### Key Commands
```bash
# Development
cd frontend
npm run dev

# Build
npm run build

# Linting
npm run lint
npm run lint:fix

# Formatting
npm run format
```

### Architecture
- **Framework**: Nuxt 3 with Vue 3 and TypeScript
- **State Management**: Pinia with persistence
- **Styling**: Tailwind CSS
- **Authentication**: JWT-based with refresh tokens
- **Real-time**: Mercure for live messaging
- **Components**: Atomic design pattern (atoms, molecules, organisms)
- **Maps**: Mapbox integration
- **Payment**: Stripe integration

### Key Directories
- `components/`: UI components organized by atomic design
- `pages/`: File-based routing
- `stores/`: Pinia state management
- `composables/`: Reusable Vue composition functions
- `types/`: TypeScript type definitions
- `middleware/`: Route guards (auth, admin, owner, guest)
- `services/`: API service layers

### Important Features
- Multi-language support (FR, EN, ES)
- Dark/light mode
- Responsive design
- SEO optimization
- Real-time messaging between users
- Booking management
- Accommodation search and filtering

## Backend (Symfony)

### Key Commands
```bash
# Development server runs in Docker
# Database migrations
php bin/console doctrine:migrations:migrate

# Static analysis
composer phpstan

# Code formatting
composer cs-fix

# Create user (admin command)
php bin/console app:create-user

# Update ratings
php bin/console app:update-ratings
```

### Architecture
- **Framework**: Symfony 7.2
- **Database**: MySQL with Doctrine ORM
- **Authentication**: JWT with LexikJWTAuthenticationBundle
- **API**: REST API with Nelmio API documentation
- **Real-time**: Mercure integration
- **Payment**: Stripe webhooks
- **Email**: Mailer component for notifications

### Key Directories
- `src/Controller/`: API controllers
- `src/Entity/`: Doctrine entities
- `src/Repository/`: Database repositories
- `src/Service/`: Business logic services
- `src/Command/`: Console commands
- `fixtures/`: Database fixtures for development

### Core Entities
- `User`: Base user entity
- `Client`: Customer users
- `Owner`: Property owners
- `Admin`: Administrative users
- `Accommodation`: Property listings
- `Booking`: Reservation records
- `Theme`: Accommodation themes
- `Comment`: Reviews and ratings
- `Ticket`: Support tickets
- `Message`: Real-time messaging

### API Endpoints
- Authentication: `/api/auth/login`, `/api/auth/refresh`
- Accommodations: `/api/accommodations`
- Bookings: `/api/bookings`
- Search: `/api/search`
- Users: `/api/users`, `/api/clients`, `/api/owners`
- Themes: `/api/themes`
- Comments: `/api/comments`

## Database

### Migrations
Database migrations are located in `backend/migrations/`. Run migrations after pulling new code:
```bash
php bin/console doctrine:migrations:migrate
```

### Fixtures
Load development data:
```bash
php bin/console hautelook:fixtures:load
```

## Real-time Features

The application uses Mercure for real-time messaging:
- **Publishing**: Backend publishes to Mercure hub
- **Subscribing**: Frontend subscribes to user-specific channels
- **Configuration**: JWT-based authentication for pub/sub

## Testing and Quality

### Frontend
- ESLint configuration with Prettier
- Component testing setup available
- TypeScript strict mode enabled

### Backend
- PHPStan for static analysis
- PHP CS Fixer for code formatting
- Doctrine schema validation

## Common Development Tasks

### Adding New Accommodation Types
1. Update `constants/accommodationTypes.ts` in frontend
2. Add corresponding entity fields in backend
3. Update fixtures if needed

### Adding New User Roles
1. Update security configuration in `config/packages/security.yaml`
2. Add middleware in frontend
3. Update API documentation

### Real-time Messaging
1. Backend publishes to Mercure: `useMercure` composable
2. Frontend subscribes to channels: `conversation.store.ts`
3. JWT tokens required for secure channels

## Important Notes

- The application uses Docker for development consistency
- JWT tokens are stored in localStorage with refresh mechanism
- File uploads are handled through dedicated endpoints
- Payment processing requires Stripe webhook configuration
- Real-time features require proper Mercure hub setup
- Multi-language content is managed through i18n configuration