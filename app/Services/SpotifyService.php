<?php

/**
 * Spotify Service - Core Business Logic for Spotify API Integration
 *
 * PURPOSE:
 * This service encapsulates all Spotify API interactions and business logic.
 * Similar to a service class in Node.js applications, this separates business logic
 * from controllers - think of it like a service in NestJS or a dedicated API client module.
 *
 * INTERACTIONS:
 * - Used by SpotifyController for web requests
 * - Used by SyncSpotifyLikedTracks command for CLI/scheduled tasks
 * - Interacts with the Spotify Web API
 * - Creates/updates SpotifyTrack and SyncHistory models
 *
 * ARCHITECTURAL ROLE:
 * In Laravel, services contain reusable business logic. This is similar to how you might
 * structure a Node.js app with separate modules for API interactions and business logic.
 * The Service Layer pattern allows the same code to be used across different entry points
 * (web, CLI, schedules) without duplication.
 */

namespace App\Services;

use App\Models\SpotifyTrack;
use App\Models\SyncHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyService
{
    protected $api;
    protected $session;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        $this->redirectUri = config('services.spotify.redirect_uri');
        $this->accessToken = config('services.spotify.access_token');

        $this->session = new Session(
            $this->clientId,
            $this->clientSecret,
            $this->redirectUri
        );

        $this->api = new SpotifyWebAPI();

        // If we have an access token stored, use it
        if ($this->accessToken) {
            $this->api->setAccessToken($this->accessToken);
        }
    }

    /**
     * Get the authorization URL for Spotify OAuth
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        $scopes = [
            'user-library-read',
            'user-read-private',
            'user-read-email',
        ];

        return $this->session->getAuthorizeUrl([
            'scope' => implode(' ', $scopes)
        ]);
    }

    /**
     * Request an access token using the authorization code
     *
     * @param string $code
     * @return array
     */
    public function requestAccessToken($code)
    {
        $this->session->requestAccessToken($code);

        $accessToken = $this->session->getAccessToken();
        $refreshToken = $this->session->getRefreshToken();
        $expiresIn = $this->session->getTokenExpiration();

        // Set the access token on the API object
        $this->api->setAccessToken($accessToken);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
        ];
    }

    /**
     * Refresh the access token using the refresh token
     *
     * @param string $refreshToken
     * @return array
     */
    public function refreshAccessToken($refreshToken)
    {
        $this->session->refreshAccessToken($refreshToken);

        $accessToken = $this->session->getAccessToken();
        $expiresIn = $this->session->getTokenExpiration();

        // Set the access token on the API object
        $this->api->setAccessToken($accessToken);

        return [
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
        ];
    }

    /**
     * Sync liked tracks from Spotify to the database
     *
     * @return SyncHistory
     */
    public function syncLikedTracks()
    {
        // Create a new sync history record
        $syncHistory = SyncHistory::create([
            'started_at' => now(),
            'status' => 'pending',
        ]);

        try {
            $limit = 50; // Maximum number of tracks per request
            $offset = 0;
            $tracksAdded = 0;
            $tracksRemoved = 0;
            $tracksUpdated = 0;
            $totalTracksProcessed = 0;
            $currentSpotifyIds = [];

            // Get all liked tracks from Spotify
            do {
                $likedTracks = $this->api->getMySavedTracks([
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                foreach ($likedTracks->items as $item) {
                    $track = $item->track;
                    $spotifyId = $track->id;
                    $currentSpotifyIds[] = $spotifyId;

                    // Get the main artist
                    $artist = !empty($track->artists) ? $track->artists[0]->name : 'Unknown Artist';

                    // Check if the track already exists in the database
                    $existingTrack = SpotifyTrack::where('spotify_id', $spotifyId)->first();

                    if ($existingTrack) {
                        // Update the existing track
                        $existingTrack->update([
                            'name' => $track->name,
                            'artist' => $artist,
                            'album' => $track->album->name ?? null,
                            'album_art_url' => $track->album->images[0]->url ?? null,
                            'preview_url' => $track->preview_url,
                            'duration_ms' => $track->duration_ms,
                            'external_url' => $track->external_urls->spotify ?? null,
                            'is_liked' => true,
                            'liked_at' => Carbon::parse($item->added_at),
                        ]);
                        $tracksUpdated++;
                    } else {
                        // Create a new track
                        SpotifyTrack::create([
                            'spotify_id' => $spotifyId,
                            'name' => $track->name,
                            'artist' => $artist,
                            'album' => $track->album->name ?? null,
                            'album_art_url' => $track->album->images[0]->url ?? null,
                            'preview_url' => $track->preview_url,
                            'duration_ms' => $track->duration_ms,
                            'external_url' => $track->external_urls->spotify ?? null,
                            'is_liked' => true,
                            'liked_at' => Carbon::parse($item->added_at),
                        ]);
                        $tracksAdded++;
                    }

                    $totalTracksProcessed++;
                }

                $offset += $limit;
            } while ($likedTracks->next);

            // Mark tracks that are no longer liked as not liked
            $tracksRemoved = SpotifyTrack::where('is_liked', true)
                ->whereNotIn('spotify_id', $currentSpotifyIds)
                ->update(['is_liked' => false]);

            // Update the sync history record
            $syncHistory->update([
                'completed_at' => now(),
                'tracks_added' => $tracksAdded,
                'tracks_removed' => $tracksRemoved,
                'tracks_updated' => $tracksUpdated,
                'total_tracks_processed' => $totalTracksProcessed,
                'status' => 'completed',
            ]);

            return $syncHistory;
        } catch (Exception $e) {
            // Log the error
            Log::error('Spotify sync failed: ' . $e->getMessage());

            // Update the sync history record with the error
            $syncHistory->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return $syncHistory;
        }
    }

    /**
     * Get the current user's profile
     *
     * @return object
     */
    public function getUserProfile()
    {
        return $this->api->me();
    }
}
