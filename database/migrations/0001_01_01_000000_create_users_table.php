<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the users table, password_reset_tokens table, and sessions table.
     *
     * RELATED MODEL ATTRIBUTES:
     *
     * 1. $fillable - In the User model, this protects sensitive fields from mass assignment
     *    - Purpose: Critical security feature that prevents unauthorized attribute setting
     *    - Example in User model:
     *      protected $fillable = [
     *          'name', 'email', 'password',
     *      ];
     *    - This means only these fields can be set using User::create() or $user->update()
     *    - Note that 'password' is fillable but receives special hashing treatment
     *    - Critical security fields like 'is_admin' would be excluded from $fillable
     *    - In JavaScript/Node.js terms, this is like whitelisting fields in a form submission
     *
     * 2. $hidden - Related to but different from $fillable, the User model typically has:
     *    - protected $hidden = ['password', 'remember_token'];
     *    - This prevents sensitive data from being serialized to JSON/arrays
     *    - Similar to excluding fields in Mongoose's toJSON options or Sequelize's attributes
     *
     * 3. $casts - In the User model, this handles data type conversion:
     *    - Example:
     *      protected $casts = [
     *          'email_verified_at' => 'datetime',
     *          'password' => 'hashed',  // Special Laravel 10+ casting
     *      ];
     *    - This is similar to defining Schema.Types in Mongoose
     *    - The 'hashed' cast is special and ensures passwords are always properly hashed
     *    - The 'datetime' cast converts database timestamps to Carbon instances
     *
     * Together, these attributes create a secure system where:
     * - Mass assignment can't set sensitive fields ($fillable protection)
     * - Sensitive data can't be accidentally exposed ($hidden protection)
     * - Data is consistently converted to proper types ($casts conversion)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                  // Auto-incrementing ID, not fillable
            $table->string('name');                        // Will be fillable, no special casting
            $table->string('email')->unique();             // Will be fillable, no special casting
            $table->timestamp('email_verified_at')->nullable(); // Cast to datetime if present
            $table->string('password');                    // Fillable but will use 'hashed' casting
            $table->rememberToken();                       // Not fillable, automatically managed
            $table->timestamps();                          // created_at/updated_at, auto-managed
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};