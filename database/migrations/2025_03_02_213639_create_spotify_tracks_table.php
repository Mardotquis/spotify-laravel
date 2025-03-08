<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the spotify_tracks table which stores information about tracks from Spotify.
     *
     * RELATED MODEL ATTRIBUTES:
     *
     * 1. $fillable - In the SpotifyTrack model, this is used to define which fields can be mass-assigned.
     *    - Purpose: It's a security feature that prevents "mass assignment vulnerabilities"
     *    - It specifies exactly which attributes can be set via create() or update() methods
     *    - Without $fillable, a malicious user could potentially set any field through mass assignment
     *    - For example:
     *      protected $fillable = [
     *          'spotify_id', 'name', 'artist', 'album', 'is_liked', ...
     *      ];
     *    - This allows code like: SpotifyTrack::create($request->all()) to safely accept only allowed fields
     *
     * 2. $casts - In the SpotifyTrack model, this defines automatic type conversion for attributes
     *    - Purpose: Ensures proper PHP data types when retrieving data from the database
     *    - Without casts, all database fields would be returned as strings
     *    - Fields in this table that benefit from casting:
     *      - 'is_liked' => 'boolean'  // Converts 0/1 from the database to true/false
     *      - 'duration_ms' => 'integer'  // Ensures numeric operations work as expected
     *      - 'liked_at' => 'datetime'  // Converts timestamp to Carbon instance for date manipulation
     *    - This allows code like: if($track->is_liked) { ... } to work with proper boolean logic
     *
     * In Laravel, both $fillable and $casts work alongside the database schema defined here,
     * creating a complete system for data validation, type safety, and security.
     */
    public function up(): void
    {
        Schema::create('spotify_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('spotify_id')->unique(); // Will be fillable, no special casting needed
            $table->string('name');                 // Will be fillable, no special casting needed
            $table->string('artist');               // Will be fillable, no special casting needed
            $table->string('album')->nullable();    // Will be fillable, no special casting needed
            $table->string('album_art_url')->nullable(); // Will be fillable, no special casting needed
            $table->string('preview_url')->nullable();   // Will be fillable, no special casting needed
            $table->integer('duration_ms')->nullable();  // Will be fillable and cast to integer
            $table->string('external_url')->nullable();  // Will be fillable, no special casting needed
            $table->boolean('is_liked')->default(true);  // Will be fillable and cast to boolean
            $table->timestamp('liked_at')->nullable();   // Will be fillable and cast to datetime
            $table->timestamps();                        // created_at/updated_at auto-managed by Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spotify_tracks');
    }
};