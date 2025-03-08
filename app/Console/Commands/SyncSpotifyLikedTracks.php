<?php

/**
 * Spotify Sync Command - CLI Interface for Syncing Liked Tracks
 *
 * PURPOSE:
 * This command provides a CLI interface to sync Spotify liked tracks.
 * In Node.js, this would be similar to a CLI command using commander.js or yargs.
 *
 * INTERACTIONS:
 * - Uses SpotifyService for business logic
 * - Provides terminal output for sync progress and results
 *
 * ARCHITECTURAL ROLE:
 * Artisan commands in Laravel (like this one) are similar to CLI scripts in Node.js.
 * They can be:
 * 1. Run manually via terminal: php artisan spotify:sync-liked
 * 2. Scheduled to run automatically (like cron jobs)
 * 3. Called programmatically from other parts of the application
 *
 * This is comparable to how you might create a bin/cli.js script in a Node.js app
 * that could be run with 'node bin/cli.js sync' or scheduled via node-cron.
 */

namespace App\Console\Commands;

use App\Services\SpotifyService;
use Illuminate\Console\Command;

class SyncSpotifyLikedTracks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:sync-liked';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync liked tracks from Spotify';

    /**
     * Execute the console command.
     * Similar to the main function in a Node.js CLI script
     */
    public function handle(SpotifyService $spotifyService)
    {
        $this->info('Starting Spotify liked tracks sync...');

        try {
            $syncHistory = $spotifyService->syncLikedTracks();

            if ($syncHistory->status === 'completed') {
                $this->info('Sync completed successfully!');
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Tracks Added', $syncHistory->tracks_added],
                        ['Tracks Removed', $syncHistory->tracks_removed],
                        ['Tracks Updated', $syncHistory->tracks_updated],
                        ['Total Processed', $syncHistory->total_tracks_processed],
                        ['Duration (seconds)', $syncHistory->getDurationInSecondsAttribute()],
                    ]
                );
            } else {
                $this->error('Sync failed: ' . $syncHistory->error_message);
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}