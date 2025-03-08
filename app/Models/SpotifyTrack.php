<?php

/**
 * SpotifyTrack Model - Data Structure for Spotify Tracks
 *
 * PURPOSE:
 * This model represents a Spotify track in the database.
 * In Node.js, this would be similar to a Mongoose model or a Sequelize model.
 *
 * INTERACTIONS:
 * - Created/updated by SpotifyService during sync
 * - Retrieved by SpotifyController for display in the UI
 *
 * ARCHITECTURAL ROLE:
 * Models in Laravel are similar to ORM models in Node.js (Mongoose, Sequelize, TypeORM, etc.).
 * They provide:
 * 1. Schema definition (via migrations)
 * 2. Data validation and casting
 * 3. Query interface
 * 4. Relationships to other models
 *
 * The $fillable and $casts properties are similar to schema definitions in Mongoose
 * or model attributes in Sequelize.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotifyTrack extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * PURPOSE:
     * The $fillable property is a security feature that prevents "mass assignment vulnerabilities".
     * It specifies exactly which attributes can be set via create() or update() methods.
     * Without $fillable, a malicious user could potentially set any field through mass assignment.
     *
     * EXAMPLES:
     * - Safe: SpotifyTrack::create(['name' => 'Song', 'artist' => 'Artist'])
     * - Safe: $track->update(['is_liked' => false])
     * - Unsafe (and prevented): $track->update(['id' => 999]) - would be blocked as 'id' isn't fillable
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose: Similar to setting schema with extra options like:
     *   { field: { type: String, required: true, default: 'value' } }
     * In Sequelize: Similar to allowNull: false or defaultValue: 'value' options
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'spotify_id',
        'name',
        'artist',
        'album',
        'album_art_url',
        'preview_url',
        'duration_ms',
        'external_url',
        'is_liked',
        'liked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * PURPOSE:
     * The $casts property ensures proper PHP data types when retrieving data from the database.
     * Without casts, all database fields would be returned as strings, regardless of their
     * actual data type in the database. Casts automatically convert between database types and PHP types.
     *
     * EXAMPLES:
     * - $track->is_liked (boolean true/false instead of "0"/"1")
     * - $track->duration_ms (integer for math operations: $track->duration_ms / 1000)
     * - $track->liked_at (Carbon datetime object with methods like ->diffForHumans())
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose: Similar to schema type definition and getters/setters
     * In Sequelize: Similar to model field types like DataTypes.BOOLEAN
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_liked' => 'boolean',
        'duration_ms' => 'integer',
        'liked_at' => 'datetime',
    ];
}
