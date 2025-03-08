<?php

/**
 * User Model - Authentication and User Profile Management
 *
 * PURPOSE:
 * This model represents a user account in the application.
 * It handles authentication, user data, and profile management.
 *
 * ARCHITECTURAL ROLE:
 * The User model is a central component in Laravel's authentication system.
 * It extends Authenticatable, which provides methods for authentication and access control.
 *
 * COMPARISON TO NODE.JS:
 * In Node.js/Express, this would be similar to:
 * - A Mongoose User model with password hashing middleware
 * - A Passport.js user configuration
 * - A JWT user payload definition
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * PURPOSE:
     * The $fillable property is a critical security feature that prevents mass assignment vulnerabilities.
     * It specifies which attributes can be safely set via User::create() or $user->update() methods.
     *
     * SECURITY IMPORTANCE:
     * Without $fillable, a malicious user could potentially inject fields like 'is_admin'=true
     * into a registration form and gain unauthorized privileges. By explicitly defining which
     * fields can be mass-assigned, Laravel blocks attempts to set non-fillable attributes.
     *
     * EXAMPLES:
     * - Safe: User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => Hash::make('password')])
     * - Blocked: User::create(['name' => 'Hacker', 'is_admin' => true]) - 'is_admin' would be ignored
     *
     * COMPARISON TO NODE.JS:
     * In Express/Mongoose, you'd need to explicitly validate and whitelist fields:
     * ```javascript
     * const { name, email, password } = req.body; // Only extract allowed fields
     * const user = new User({ name, email, password }); // Create with only those fields
     * ```
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * PURPOSE:
     * The $hidden property prevents sensitive attributes from being included when the model
     * is converted to an array or JSON. This is critical for preventing password leaks and
     * protecting sensitive user data.
     *
     * SECURITY IMPORTANCE:
     * When you return a User model in a controller or API response, $hidden ensures that
     * sensitive fields like passwords are automatically removed from the response.
     *
     * EXAMPLES:
     * - $user->toArray() // 'password' and 'remember_token' will be excluded
     * - json_encode($user) // Sensitive fields are excluded from the JSON
     * - response()->json($user) // API responses won't leak sensitive data
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose, you'd use schema options:
     * ```javascript
     * const userSchema = new Schema({
     *   password: { type: String, select: false }, // Exclude from query results
     * });
     *
     * // Or when converting to JSON:
     * userSchema.methods.toJSON = function() {
     *   const userObject = this.toObject();
     *   delete userObject.password;
     *   return userObject;
     * };
     * ```
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * PURPOSE:
     * The $casts property defines automatic type conversion between database values and PHP data types.
     * It ensures that attributes are consistently converted to the appropriate PHP types when accessed.
     *
     * EXAMPLES:
     * - 'email_verified_at' => 'datetime' // Returns a Carbon instance instead of a string
     *   $user->email_verified_at->diffForHumans() // "3 days ago"
     *
     * - 'password' => 'hashed' // Special Laravel 10+ casting that ensures passwords are always hashed
     *   $user->password = 'new-password' // Automatically hashed when saved
     *
     * BENEFITS:
     * - Type consistency throughout application
     * - Automatic data transformation (like date formatting)
     * - Security for sensitive fields (like password hashing)
     *
     * COMPARISON TO NODE.JS:
     * In Mongoose, you'd use schema types, getters/setters, and middleware:
     * ```javascript
     * const userSchema = new Schema({
     *   createdAt: { type: Date },
     *   password: {
     *     type: String,
     *     set: (value) => bcrypt.hashSync(value, 10) // Hash on set
     *   }
     * });
     * ```
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
