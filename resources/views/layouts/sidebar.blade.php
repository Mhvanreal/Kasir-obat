<aside class="hidden w-64 bg-white border-r border-gray-200 lg:block">
    <div class="flex flex-col h-full">
        <div class="flex-1 px-3 py-4 overflow-y-auto">
            <h3 class="px-4 mb-4 text-xs font-semibold tracking-wider text-teal-600 uppercase">Menu Apotek</h3>
            <nav class="space-y-1">
                @if (auth()->user()->role === 'admin')
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Kelola Obat -->
                    <a href="{{ route('apoteker.obat.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('apoteker.obat*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('apoteker.obat*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Kelola Obat
                    </a>

                    <!-- Penjualan -->
                    <a href="{{ route('kasir.transaksi') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('kasir.transaksi*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kasir.transaksi*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Transaksi Penjualan
                    </a>

                    <!-- Pembelian -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Pembelian Stok
                    </a>

                    <!-- Supplier -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Data Supplier
                    </a>

                    <!-- Pelanggan -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Pelanggan
                    </a>

                    <!-- Divider -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Laporan -->
                    <a href="{{ route('owner.laporan') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('owner.laporan*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('owner.laporan*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>

                    <!-- User Management -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Kelola Pengguna
                    </a>
                @endif
            </nav>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Toggle Button -->
<div class="fixed z-50 bottom-4 right-4 lg:hidden">
    <button id="mobileSidebarToggle"
        class="flex items-center justify-center w-12 h-12 text-white transition-all bg-gradient-to-r from-teal-600 to-emerald-600 rounded-full shadow-lg hover:shadow-xl">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-gray-900 bg-opacity-50 lg:hidden"></div>

<!-- Mobile Sidebar -->
<aside id="mobileSidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 transition-transform duration-300 transform -translate-x-full bg-white shadow-xl lg:hidden">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-teal-600 to-emerald-600">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1 16h2v2h-2v-2zm0-10h2v8h-2V8z" />
                </svg>
                <span class="text-lg font-bold text-white">Menu</span>
            </div>
            <button id="closeMobileSidebar" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="flex-1 px-3 py-4 overflow-y-auto">
            <nav class="space-y-1">
                @if (auth()->user()->role === 'admin')
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Kelola Obat -->
                    <a href="{{ route('apoteker.obat.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('apoteker.obat*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('apoteker.obat*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Kelola Obat
                    </a>

                    <!-- Penjualan -->
                    <a href="{{ route('kasir.transaksi') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('kasir.transaksi*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kasir.transaksi*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Transaksi Penjualan
                    </a>

                    <!-- Pembelian -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Pembelian Stok
                    </a>

                    <!-- Supplier -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Data Supplier
                    </a>

                    <!-- Pelanggan -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Pelanggan
                    </a>

                    <!-- Divider -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Laporan -->
                    <a href="{{ route('owner.laporan') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('owner.laporan*') ? 'text-white bg-gradient-to-r from-teal-600 to-emerald-600' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('owner.laporan*') ? 'text-white' : 'text-teal-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>

                    <!-- User Management -->
                    <a href="#"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                        <svg class="w-5 h-5 mr-3 text-teal-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Kelola Pengguna
                    </a>
                @endif
            </nav>
        </div>
    </div>
</aside>

@push('scripts')
    <script>
        // Mobile Sidebar Toggle
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeMobileSidebar = document.getElementById('closeMobileSidebar');

        function openMobileSidebar() {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileSidebarOverlay.classList.remove('hidden');
        }

        function closeMobileSidebarFunc() {
            mobileSidebar.classList.add('-translate-x-full');
            mobileSidebarOverlay.classList.add('hidden');
        }

        mobileSidebarToggle.addEventListener('click', openMobileSidebar);
        closeMobileSidebar.addEventListener('click', closeMobileSidebarFunc);
        mobileSidebarOverlay.addEventListener('click', closeMobileSidebarFunc);
    </script>
@endpush
