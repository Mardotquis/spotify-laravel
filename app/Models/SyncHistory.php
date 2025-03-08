<?php

/**
 * SyncHistory Model - Data Structure for Sync Operations
 *
 * PURPOSE:
 * This model tracks the history and results of Spotify sync operations.
 * In Node.js, this would be similar to a Mongoose model or a Sequelize model.
 *
 * INTERACTIONS:
 * - Created/updated by SpotifyService during sync operations
 * - Retrieved by SpotifyController for display in the UI
 *
 * ARCHITECTURAL ROLE:
 * Like the SpotifyTrack model, this follows the Active Record pattern in Laravel.
 * This is similar to how you'd use Mongoose or Sequelize in Node.js to:
 * 1. Define a schema
 * 2. Perform CRUD operations
 * 3. Define getters/setters and virtual properties
 *
 * The getDurationInSecondsAttribute method is similar to a Mongoose virtual or
 * a Sequelize getter method.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * PURPOSE:
     * The $fillable property protects against mass assignment vulnerabilities by specifying
     * which attributes can be set via create() or update() methods. This is a critical security
     * feature in Laravel that prevents unauthorized attribute manipulation.
     *
     * EXAMPLES:
     * - Creating a sync record:
     *   SyncHistory::create(['started_at' => now(), 'status' => 'pending'])
     * - Updating a sync record:
     *   $syncHistory->update(['completed_at' => now(), 'status' => 'completed'])
     *
     * SECURITY IMPLICATIONS:
     * Without $fillable, if you passed user input directly to create() or update(),
     * a malicious user could potentially set fields they shouldn't have access to,
     * like manipulating tracking statistics or status fields.
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose: Similar to defining which fields are required/optional in a schema
     * In Sequelize: Similar to defining model attributes with allowNull configuration
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'started_at',
        'completed_at',
        'tracks_added',
        'tracks_removed',
        'tracks_updated',
        'total_tracks_processed',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * PURPOSE:
     * The $casts property provides automatic type conversion between database values and PHP values.
     * This ensures that when you access attributes, they're already in the correct PHP data type,
     * making your code more reliable and preventing type-related bugs.
     *
     * EXAMPLES:
     * - $syncHistory->tracks_added (integer for calculations: $total = $added + $updated)
     * - $syncHistory->started_at (Carbon instance for date methods: $started_at->diffForHumans())
     * - $syncHistory->completed_at (Carbon instance for comparison: $completed_at->gt($started_at))
     *
     * BENEFITS:
     * - Ensures consistent data types throughout your application
     * - Enables type-specific operations without manual conversion
     * - Improves code readability by removing manual typecasting
     * - Makes date/time manipulation much easier with Carbon methods
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose: Similar to Schema.Types definitions and Mongoose's automatic casting
     * In Sequelize: Similar to DataTypes definition in model attributes
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'tracks_added' => 'integer',
        'tracks_removed' => 'integer',
        'tracks_updated' => 'integer',
        'total_tracks_processed' => 'integer',
    ];

    /**
     * Get the duration of the sync in seconds.
     *
     * PURPOSE:
     * This accessor (or "computed attribute") calculates the duration between start and completion.
     * It's a virtual attribute that doesn't exist in the database but is calculated on-the-fly.
     *
     * USAGE:
     * $syncHistory->duration_in_seconds // Access like a normal property
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose: Similar to a virtual property with a getter
     * In Sequelize: Similar to a virtual attribute or getter method
     *
     * @return int|null
     */
    public function getDurationInSecondsAttribute()
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }
}
