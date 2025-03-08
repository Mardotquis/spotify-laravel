{{--
 * Spotify Index View - Main Dashboard UI
 *
 * PURPOSE:
 * This view displays the main dashboard, including:
 * - Current sync status
 * - Sync history
 * - List of liked tracks
 *
 * In Node.js, this would be similar to a view in EJS, Handlebars, Pug,
 * or a component in React/Vue.
 *
 * INTERACTIONS:
 * - Extends the app layout
 * - Displays data from the SpotifyTrack and SyncHistory models
 * - Provides UI for viewing track details
 *
 * ARCHITECTURAL ROLE:
 * Views in Laravel Blade are similar to view templates in Express.
 * - @extends is like layout inheritance in template engines
 * - @section/@yield is like blocks/partials in template engines
 * - Blade directives (@if, @foreach) are like template conditionals/loops
 *
 * This file would be similar to a pages/index.ejs or components/Dashboard.jsx
 * in a Node.js application.
--}}

@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            {{-- Sync Status Section --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900">Sync Status</h2>
                @if ($lastSync)
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Last Sync</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $lastSync->completed_at ? $lastSync->completed_at->diffForHumans() : 'Never' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="mt-1">
                                    @if ($lastSync->status === 'completed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                            Completed
                                        </span>
                                    @elseif($lastSync->status === 'failed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-accent-100 text-accent-800">
                                            Failed
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tracks Processed</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $lastSync->total_tracks_processed }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No sync history available</p>
                @endif
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900">Recent Syncs</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Added</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Removed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($syncHistory as $sync)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sync->started_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($sync->status === 'completed')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                Completed
                                            </span>
                                        @elseif($sync->status === 'failed')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800"
                                                title="{{ $sync->error_message }}">
                                                Failed
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sync->tracks_added }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sync->tracks_updated }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sync->tracks_removed }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sync->getDurationInSecondsAttribute() ? $sync->getDurationInSecondsAttribute() . 's' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900">Liked Tracks</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($tracks as $track)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors duration-150 cursor-pointer"
                            onclick="showTrackDetails('{{ json_encode($track) }}')">
                            <div class="flex space-x-4">
                                @if ($track->album_art_url)
                                    <img src="{{ $track->album_art_url }}" alt="{{ $track->name }}"
                                        class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 truncate">{{ $track->name }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $track->artist }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $track->album }}</p>
                                    @if ($track->preview_url)
                                        <audio controls class="mt-2 w-full h-8">
                                            <source src="{{ $track->preview_url }}" type="audio/mpeg">
                                        </audio>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tracks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
