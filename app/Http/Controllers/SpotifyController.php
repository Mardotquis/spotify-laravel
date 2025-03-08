<?php

/**
 * Spotify Controller - Web Request Handler
 *
 * PURPOSE:
 * This controller handles HTTP requests for Spotify-related functionality.
 * In Laravel, controllers are similar to route handlers in Express.js or controllers in NestJS.
 *
 * INTERACTIONS:
 * - Uses SpotifyService for business logic and API interactions
 * - Interacts with SpotifyTrack and SyncHistory models for data retrieval
 * - Renders views for UI display
 *
 * ARCHITECTURAL ROLE:
 * Controllers in Laravel are responsible for:
 * 1. Receiving HTTP requests (like Express middleware/handlers)
 * 2. Validating inputs (similar to express-validator)
 * 3. Delegating business logic to services (like service calls in Node.js apps)
 * 4. Returning responses (views, redirects, JSON, etc.)
 *
 * In a Node.js/Express app, this would be similar to your route handlers that
 * import service modules to handle business logic.
 */

namespace App\Http\Controllers;

use App\Models\SpotifyTrack;
use App\Models\SyncHistory;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * Display the main dashboard with tracks and sync history
     * Similar to a GET route handler in Express.js
     */
    public function index()
    {
        $tracks = SpotifyTrack::where('is_liked', true)
            ->orderBy('liked_at', 'desc')
            ->paginate(50);

        $syncHistory = SyncHistory::latest()
            ->take(5)
            ->get();

        $lastSync = $syncHistory->first();

        return view('spotify.index', compact('tracks', 'syncHistory', 'lastSync'));
    }

    /**
     * Redirect to Spotify OAuth authorization
     * Similar to implementing OAuth flow in Express.js
     */
    public function auth()
    {
        $url = $this->spotifyService->getAuthorizationUrl();
        return redirect($url);
    }

    /**
     * Handle the OAuth callback from Spotify
     * Similar to an OAuth callback route in Express.js
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect()->route('spotify.index')
                ->with('error', 'Authorization failed');
        }

        try {
            $tokens = $this->spotifyService->requestAccessToken($code);

            // Store tokens in .env or database
            // For simplicity, we'll update the .env file directly
            $this->updateEnvFile([
                'SPOTIFY_ACCESS_TOKEN' => $tokens['access_token'],
                'SPOTIFY_REFRESH_TOKEN' => $tokens['refresh_token'],
            ]);

            return redirect()->route('spotify.index')
                ->with('success', 'Successfully connected to Spotify!');
        } catch (\Exception $e) {
            return redirect()->route('spotify.index')
                ->with('error', 'Failed to authenticate with Spotify: ' . $e->getMessage());
        }
    }

    /**
     * Trigger a manual sync of liked tracks
     * Similar to a POST route handler in Express.js
     */
    public function sync()
    {
        try {
            $syncHistory = $this->spotifyService->syncLikedTracks();

            $message = $syncHistory->status === 'completed'
                ? 'Successfully synced tracks!'
                : 'Sync failed: ' . $syncHistory->error_message;

            return redirect()->route('spotify.index')
                ->with($syncHistory->status === 'completed' ? 'success' : 'error', $message);
        } catch (\Exception $e) {
            return redirect()->route('spotify.index')
                ->with('error', 'Failed to sync tracks: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to update .env file
     * Similar to a config update utility in Node.js
     */
    protected function updateEnvFile(array $data)
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        foreach ($data as $key => $value) {
            // Update existing value
            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            }
            // Add new value
            else {
                $content .= "\n{$key}={$value}\n";
            }
        }

        file_put_contents($path, $content);
    }
}