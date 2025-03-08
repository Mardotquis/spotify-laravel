# Spotify Liked Songs Sync

A Laravel application to synchronize your Spotify liked songs with a local database and display them in a clean web interface. This README provides a comprehensive overview of the application's architecture and how it compares to typical Node.js patterns.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Laravel vs Node.js Concepts](#laravel-vs-nodejs-concepts)
- [Project Structure](#project-structure)
- [Key Components](#key-components)
- [Setup & Installation](#setup--installation)
- [Running the Application](#running-the-application)
- [Deployment Options](#deployment-options)

## Overview

This application allows you to:

1. Connect to your Spotify account via OAuth
2. Sync your liked tracks to a local database
3. View your liked tracks with album art and previews
4. See sync history and statistics
5. Run syncs manually or on a schedule

## Architecture

The application follows the MVC (Model-View-Controller) architecture pattern:

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│    Routes   │──────▶ Controllers │──────▶   Services  │──────▶    Models   │
└─────────────┘      └─────────────┘      └─────────────┘      └─────────────┘
                            │                                        │
                            ▼                                        │
                     ┌─────────────┐                                 │
                     │    Views    │◀────────────────────────────────┘
                     └─────────────┘
```

- **Routes**: Define URLs and map them to controllers (like Express routes)
- **Controllers**: Handle HTTP requests and responses (like Express route handlers)
- **Services**: Contain business logic (like service modules in Node.js)
- **Models**: Represent database entities (like Mongoose/Sequelize models)
- **Views**: Display data to users (like EJS/Handlebars templates or React components)

## Laravel vs Node.js Concepts

| Laravel Concept | Node.js Equivalent | Description |
|-----------------|-------------------|-------------|
| Routes (web.php) | Express Router | Define URL endpoints and map them to controllers |
| Controllers | Express route handlers | Process HTTP requests and return responses |
| Middleware | Express middleware | Filter HTTP requests before they reach controllers |
| Models | Mongoose/Sequelize models | Represent database entities with validations |
| Eloquent ORM | Mongoose/Sequelize | Database abstraction layer for querying |
| Blade Templates | EJS/Handlebars/Pug | Server-side templating for generating HTML |
| Services | Custom service modules | Business logic extracted from controllers |
| Artisan Commands | CLI scripts | Command-line utilities for various tasks |
| Scheduler | node-cron/crontab | Schedule tasks to run automatically |
| Migrations | Migrations in Sequelize | Version control for database schema |
| Service Providers | Module exports/DI frameworks | Register and bootstrap application services |
| Facades | Exported modules | Static-like interface to underlying services |

## Project Structure

Key files and directories:

```
├── app/
│   ├── Console/
│   │   └── Commands/              # CLI commands (like Node.js CLI scripts)
│   │       └── SyncSpotifyLikedTracks.php
│   ├── Http/
│   │   └── Controllers/           # HTTP controllers (like Express route handlers)
│   │       └── SpotifyController.php
│   ├── Models/                    # Database models (like Mongoose/Sequelize models)
│   │   ├── SpotifyTrack.php
│   │   └── SyncHistory.php
│   └── Services/                  # Business logic (like service modules in Node.js)
│       └── SpotifyService.php
├── config/
│   └── services.php               # Service configuration (like .env in Node.js)
├── database/
│   └── migrations/                # Database migrations (like Sequelize migrations)
├── resources/
│   └── views/                     # Blade templates (like EJS/Handlebars in Node.js)
│       ├── layouts/
│       │   └── app.blade.php
│       └── spotify/
│           └── index.blade.php
└── routes/
    └── web.php                    # Route definitions (like Express router)
```

## Key Components

### Models

Models define the database structure and relationships, similar to Mongoose or Sequelize models in Node.js.

#### SpotifyTrack

```php
// Similar to a Mongoose schema or Sequelize model in Node.js
protected $fillable = [
    'spotify_id',
    'name',
    'artist',
    // ...
];

// Similar to SchemaTypes in Mongoose or DataTypes in Sequelize
protected $casts = [
    'is_liked' => 'boolean',
    'duration_ms' => 'integer',
    'liked_at' => 'datetime',
];
```

#### SyncHistory

Tracks the history and results of sync operations, with timestamp tracking and status information.

### Services

Services contain business logic, similar to service modules in a Node.js application.

#### SpotifyService

Handles all Spotify API interactions, including:
- OAuth authentication
- Fetching liked tracks
- Syncing tracks to the database

This is similar to how you might create an API service in Node.js:

```javascript
// Node.js equivalent
class SpotifyService {
  constructor(config) {
    this.api = new SpotifyWebApi({
      clientId: config.clientId,
      clientSecret: config.clientSecret
    });
  }

  async syncLikedTracks() {
    // Implementation...
  }
}
```

### Controllers

Controllers handle HTTP requests and responses, similar to route handlers in Express.js.

#### SpotifyController

Handles web routes related to Spotify functionality:
- Displaying the main dashboard
- Handling OAuth authentication
- Manual sync triggers

This is comparable to Express.js route handlers:

```javascript
// Node.js/Express equivalent
app.get('/', async (req, res) => {
  const tracks = await SpotifyTrack.find({ isLiked: true })
    .sort({ likedAt: 'desc' })
    .limit(50);

  res.render('index', { tracks });
});
```

### Commands

Commands provide CLI interfaces, similar to custom scripts in a Node.js application's package.json.

#### SyncSpotifyLikedTracks

```php
// Run with: php artisan spotify:sync-liked
// Similar to a CLI script in Node.js
public function handle(SpotifyService $spotifyService)
{
    $this->info('Starting Spotify liked tracks sync...');
    $syncHistory = $spotifyService->syncLikedTracks();
    // ...
}
```

### Views

Blade templates are similar to template engines in Node.js like EJS, Handlebars, or Pug.

```blade
{{-- Similar to EJS or Handlebars in Node.js --}}
@foreach($tracks as $track)
    <div>{{ $track->name }}</div>
@endforeach
```

## Setup & Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and configure:
   ```
   SPOTIFY_CLIENT_ID=your_client_id
   SPOTIFY_CLIENT_SECRET=your_client_secret
   SPOTIFY_REDIRECT_URI=http://localhost:8000/callback
   ```
4. Run migrations:
   ```bash
   php artisan migrate
   ```
5. Start the development server:
   ```bash
   php artisan serve
   ```

## Running the Application

1. Visit http://localhost:8000
2. Click "Connect Spotify" to authenticate
3. After authentication, click "Sync Now" to fetch your liked tracks
4. View your tracks and sync history on the dashboard

## Deployment Options

The application can be deployed in several ways:

1. **Traditional Hosting**: Apache/Nginx + PHP + MySQL
2. **Containerized**: Docker + Docker Compose
3. **Serverless**: AWS Lambda + API Gateway + Aurora Serverless

For serverless deployment, the application can be configured to:
- Spin up only when someone visits the site
- Run scheduled syncs via AWS EventBridge
- Scale to zero when not in use for cost efficiency

See the serverless deployment documentation for more details.
