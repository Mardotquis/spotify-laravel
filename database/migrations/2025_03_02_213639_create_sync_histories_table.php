<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the sync_histories table which tracks Spotify sync operations.
     *
     * RELATED MODEL ATTRIBUTES:
     *
     * 1. $fillable - In the SyncHistory model, this is used to define which fields can be mass-assigned.
     *    - Purpose: It's a security feature that prevents "mass assignment vulnerabilities"
     *    - It allows specific fields to be set via create() or update() methods
     *    - For example:
     *      protected $fillable = [
     *          'started_at', 'completed_at', 'tracks_added', 'status', ...
     *      ];
     *    - When creating a new sync record: SyncHistory::create(['started_at' => now(), 'status' => 'pending'])
     *    - This protects sensitive/critical fields from unauthorized modification
     *    - In Node.js with Mongoose, this is similar to setting "select: false" for sensitive fields
     *
     * 2. $casts - In the SyncHistory model, this defines automatic type conversion for attributes
     *    - Purpose: Provides automatic type conversion between database and PHP types
     *    - Fields in this table that benefit from casting:
     *      - Integer fields ('tracks_added', 'tracks_removed', etc.) => 'integer'
     *      - Timestamp fields ('started_at', 'completed_at') => 'datetime'
     *    - Example benefits:
     *      - Datetime casts give you Carbon methods: $history->started_at->diffForHumans()
     *      - Integer casts ensure math operations work correctly: $history->tracks_added + 5
     *    - This is similar to defining schema types in Mongoose or data types in Sequelize
     *
     * 3. Custom Accessors/Mutators - The SyncHistory model also has a custom accessor:
     *    - getDurationInSecondsAttribute() - computes the duration between start and completion
     *    - This is similar to virtual properties in Mongoose or getters in Sequelize
     *    - Can be accessed as: $history->duration_in_seconds
     *
     * These model features complement the database schema, creating a complete
     * system for data integrity, type safety, and developer convenience.
     */
    public function up(): void
    {
        Schema::create('sync_histories', function (Blueprint $table) {
            $table->id();                                       // Auto-incrementing ID, not fillable
            $table->timestamp('started_at');                    // Will be fillable and cast to datetime
            $table->timestamp('completed_at')->nullable();      // Will be fillable and cast to datetime
            $table->integer('tracks_added')->default(0);        // Will be fillable and cast to integer
            $table->integer('tracks_removed')->default(0);      // Will be fillable and cast to integer
            $table->integer('tracks_updated')->default(0);      // Will be fillable and cast to integer
            $table->integer('total_tracks_processed')->default(0); // Will be fillable and cast to integer
            $table->string('status')->default('pending');       // Will be fillable, no special casting needed
            $table->text('error_message')->nullable();          // Will be fillable, no special casting needed
            $table->timestamps();                               // created_at/updated_at auto-managed by Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_histories');
    }
};