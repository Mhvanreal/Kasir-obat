<nav class="sticky top-0 z-50 bg-gradient-to-r from-teal-600 to-emerald-600 shadow-lg">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo Section -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1 16h2v2h-2v-2zm0-10h2v8h-2V8z" />
                        <path d="M11 8h2v8h-2V8zm0 10h2v2h-2v-2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-white sm:text-xl">Apotek Citra</span>
                    <p class="hidden text-xs text-teal-100 sm:block">Sistem Manajemen Farmasi</p>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative">
                <button id="userDropdownButton" type="button"
                    class="flex items-center px-4 py-2 space-x-2 text-sm text-white transition-all duration-150 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 backdrop-blur-sm">
                    <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <span class="hidden font-medium sm:inline">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="userDropdownMenu"
                    class="absolute right-0 hidden w-48 mt-2 origin-top-right bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="py-1">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            <span
                                class="inline-block px-2 py-1 mt-2 text-xs font-semibold text-teal-700 bg-teal-100 rounded-full">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>

                        <!-- Profile Link -->
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-teal-50 hover:text-teal-700">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile Saya
                        </a>

                        <!-- Divider -->
                        <div class="border-t border-gray-100"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-left text-red-600 transition-colors hover:bg-red-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        // User Dropdown Toggle
        const userDropdownButton = document.getElementById('userDropdownButton');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        userDropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdownButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.add('hidden');
            }
        });

        // Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userDropdownMenu.classList.add('hidden');
            }
        });
    </script>
@endpush
