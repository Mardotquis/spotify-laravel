{{--
 * Layout Template - Application UI Structure
 *
 * PURPOSE:
 * This layout defines the common HTML structure shared across all pages.
 * In a Node.js app, this would be similar to a layout in EJS, Handlebars, Pug, or a
 * main App component in React/Vue.
 *
 * INTERACTIONS:
 * - Contains common elements like navigation, header, footer
 * - Includes Tailwind CSS for styling
 * - Defines the modal component used for track details
 * - Contains JavaScript functions for UI interactions
 *
 * ARCHITECTURAL ROLE:
 * Laravel Blade templates are similar to server-side template engines in Node.js.
 * The @yield directive is similar to {{{body}}} in Handlebars or <ng-content> in Angular.
 * Content from child views is inserted at these yield points.
 *
 * This is similar to how you might structure a layout in Express.js with a template engine:
 * ```
 * <!-- layout.ejs -->
 * <html>
 *   <head>...</head>
 *   <body>
 *     <nav>...</nav>
 *     <%- body %>
 *   </body>
 * </html>
 * ```
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Spotify Liked Songs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        {{-- Navigation bar (similar to a shared component in React/Vue) --}}
        <nav class="bg-primary-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('spotify.index') }}" class="text-white text-xl font-bold tracking-tight">
                                {{ config('app.name') }}
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('spotify.auth') }}"
                            class="text-white bg-accent-500 hover:bg-accent-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            Connect Spotify
                        </a>
                        <a href="{{ route('spotify.sync') }}"
                            class="text-primary-600 bg-white hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            Sync Now
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main content area (where child views are inserted) --}}
        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Flash messages (similar to connect-flash in Express) --}}
                @if (session('success'))
                    <div class="mb-4 bg-primary-100 border border-primary-400 text-primary-700 px-4 py-3 rounded-lg relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-accent-100 border border-accent-400 text-accent-700 px-4 py-3 rounded-lg relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Content from child views is inserted here (like {{{body}}} in Handlebars) --}}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Modal component (similar to a React modal component) --}}
    <div id="trackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 overflow-hidden shadow-xl transform transition-all">
            <div class="bg-primary-600 px-6 py-4">
                <h3 class="text-lg font-medium text-white tracking-tight" id="modalTitle">Track Details</h3>
            </div>
            <div class="px-6 py-4" id="modalContent">
                <!-- Content will be dynamically inserted here -->
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-150">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- JavaScript for interactivity (similar to client-side JS in Express) --}}
    <script>
        function showTrackDetails(track) {
            const modal = document.getElementById('trackModal');
            const modalContent = document.getElementById('modalContent');
            const modalTitle = document.getElementById('modalTitle');

            // Parse the track data from the data attribute
            const trackData = JSON.parse(track);

            // Update modal title
            modalTitle.textContent = trackData.name;

            // Create content HTML
            const content = `
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-start space-x-4">
                        ${trackData.album_art_url
                            ? `<img src="${trackData.album_art_url}" alt="${trackData.name}" class="w-32 h-32 rounded-lg object-cover">`
                            : `<div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center"><span class="text-gray-400">No Image</span></div>`
                        }
                        <div class="flex-1">
                            <h4 class="font-medium text-lg text-gray-900">${trackData.name}</h4>
                            <p class="text-gray-600">${trackData.artist}</p>
                            <p class="text-gray-500 text-sm">${trackData.album || 'No Album'}</p>
                            ${trackData.preview_url
                                ? `<audio controls class="mt-2 w-full"><source src="${trackData.preview_url}" type="audio/mpeg"></audio>`
                                : ''
                            }
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <dl class="grid grid-cols-1 gap-3">
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Spotify ID</dt>
                                <dd class="text-sm text-gray-900 col-span-2">${trackData.spotify_id}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="text-sm text-gray-900 col-span-2">${Math.floor(trackData.duration_ms / 1000 / 60)}:${String(Math.floor(trackData.duration_ms / 1000 % 60)).padStart(2, '0')}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Liked Status</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${trackData.is_liked ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800'}">
                                        ${trackData.is_liked ? 'Liked' : 'Not Liked'}
                                    </span>
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Liked At</dt>
                                <dd class="text-sm text-gray-900 col-span-2">${trackData.liked_at || 'N/A'}</dd>
                            </div>
                            ${trackData.external_url ? `
                                        <div class="grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500">Spotify Link</dt>
                                            <dd class="text-sm text-primary-600 hover:text-primary-700 col-span-2">
                                                <a href="${trackData.external_url}" target="_blank" class="underline">Open in Spotify</a>
                                            </dd>
                                        </div>
                                    ` : ''}
                        </dl>
                    </div>
                </div>
            `;

            modalContent.innerHTML = content;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('trackModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('trackModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('trackModal').classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</body>

</html>
