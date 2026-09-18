@extends('layouts.app')

@section('title', 'Transaksi Penjualan')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8" x-data="transaksiData()">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div
                    class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Transaksi Penjualan</h1>
                    <p class="text-sm text-gray-600">Pilih obat dari katalog lalu lengkapi ringkasan transaksi</p>
                </div>
            </div>
        </div>

        {{-- Flash success/error ditampilkan otomatis lewat <x-flash-toast /> di layouts.app --}}

        <!-- Inline Notification -->
        <div x-show="notif" x-transition x-cloak
            class="fixed top-4 right-4 z-50 px-5 py-3 text-sm font-medium text-white bg-yellow-600 rounded-lg shadow-lg">
            <span x-text="notif"></span>
        </div>

        <!-- Transaksi Berjalan (Live Transactions) -->
        @if($penjualans->count() > 0 && $penjualans->first()->created_at->diffInMinutes(now()) < 5)
        <div class="mb-6 overflow-hidden bg-gradient-to-r from-teal-50 to-emerald-50 border-2 border-teal-500 rounded-lg shadow-md"
            x-data="{ showLive: true }" x-show="showLive" x-transition>
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <svg class="w-6 h-6 ml-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-teal-900">Transaksi Berjalan</p>
                            <p class="text-xs text-teal-700">{{ $penjualans->take(3)->count() }} transaksi terbaru (5 menit terakhir)</p>
                        </div>
                    </div>
                    <button @click="showLive = false" class="text-teal-600 hover:text-teal-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                    @foreach($penjualans->take(3) as $penjualan)
                        @if($penjualan->created_at->diffInMinutes(now()) < 5)
                        <div class="p-3 bg-white rounded-lg border border-teal-200 shadow-sm">
                            <div class="flex items-start justify-between mb-2">
                                <span class="px-2 py-0.5 text-xs font-semibold text-white bg-teal-600 rounded-full">
                                    {{ $penjualan->nota }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $penjualan->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pelanggan:</span>
                                    <span class="font-medium text-gray-900">{{ $penjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pembayaran:</span>
                                    <span class="font-medium text-gray-900 uppercase">{{ $penjualan->metode_pembayaran }}</span>
                                </div>
                                <div class="flex justify-between pt-1 border-t border-gray-200">
                                    <span class="text-gray-900 font-semibold">Total:</span>
                                    <span class="font-bold text-teal-600">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Side: Katalog Obat Full Width -->
            <div class="lg:col-span-2">
                <!-- Katalog Card -->
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Katalog Obat</h3>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" x-model="pencarian"
                                class="block w-64 py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                placeholder="Cari obat...">
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                            <template x-for="obat in filteredObats" :key="obat.kd_obat">
                                <div
                                    class="relative flex flex-col overflow-hidden transition-all duration-200 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-lg">
                                    <div class="h-32 overflow-hidden bg-gray-100">
                                        <img :src="obat.gambar ? '/storage/' + obat.gambar : '/images/no-image.svg'"
                                            :alt="obat.nm_obat" class="object-cover w-full h-full">
                                    </div>
                                    <span
                                        :class="{
                                            'bg-red-600 text-white': obat.stok <= 0,
                                            'bg-red-100 text-red-800 ring-1 ring-red-300': obat.stok > 0 && obat.stok < 10,
                                            'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300': obat.stok >= 10 && obat.stok <= 20,
                                            'bg-green-100 text-green-800': obat.stok > 20,
                                        }"
                                        class="absolute top-2 right-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold">
                                        <span x-text="obat.stok <= 0 ? 'HABIS' : (obat.stok < 10 ? 'Kritis: ' + obat.stok : 'Stok ' + obat.stok)"></span>
                                    </span>
                                    <div class="flex flex-col flex-1 p-3">
                                        <p class="text-sm font-semibold text-gray-900" x-text="obat.nm_obat"></p>
                                        <p class="text-xs text-gray-500" x-text="(obat.jenis || '') + ' · ' + (obat.satuan || '')"></p>
                                        <p class="mt-1 text-sm font-bold text-teal-600"
                                            x-text="'Rp ' + Number(obat.harga_jual).toLocaleString('id-ID')"></p>
                                        <button type="button"
                                            x-show="obat.stok > 0"
                                            @click="tambah(obat)"
                                            class="inline-flex items-center justify-center px-3 py-1.5 mt-3 text-xs font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah
                                        </button>
                                        <button type="button" x-show="obat.stok === 0" disabled
                                            class="inline-flex items-center justify-center px-3 py-1.5 mt-3 text-xs font-semibold text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            Stok Habis
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="filteredObats.length === 0" class="py-10 text-center">
                            <p class="text-sm text-gray-500">Tidak ada obat yang cocok</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Ringkasan Transaksi + Keranjang + Riwayat (Tabs) -->
            <div class="lg:col-span-1" x-data="{ activeTab: 'transaksi' }">
                <div class="sticky top-6 overflow-hidden bg-white rounded-lg shadow-md">
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-200">
                        <button @click="activeTab = 'transaksi'" 
                            :class="activeTab === 'transaksi' ? 'border-teal-500 text-teal-600 bg-teal-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Transaksi
                        </button>
                        <button @click="activeTab = 'riwayat'"
                            :class="activeTab === 'riwayat' ? 'border-teal-500 text-teal-600 bg-teal-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Riwayat
                        </button>
                    </div>

                    <!-- Tab Content: Transaksi -->
                    <div x-show="activeTab === 'transaksi'" class="p-6">
                        <form action="{{ route('karyawan.transaksi.store') }}" method="POST">
                            @csrf

                            <!-- Hidden cart items (wrapper div wajib: Alpine v3 hanya render 1 root element di <template x-for>) -->
                            <template x-for="(item, index) in cart" :key="'cart-' + item.kd_obat + '-' + index">
                                <div class="hidden">
                                    <input type="hidden" :name="'items[' + index + '][kd_obat]'" :value="item.kd_obat">
                                    <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                                </div>
                            </template>
                            <!-- Hidden metode pembayaran (di-set oleh prosesTransaksi()) -->
                            <input type="hidden" name="metode_pembayaran" x-model="metodePembayaran">

                            <!-- Pelanggan -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Pelanggan</label>
                                    <button type="button" @click="showPelangganBaru = true"
                                        class="inline-flex items-center text-xs font-medium text-teal-600 hover:text-teal-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Pelanggan Baru
                                    </button>
                                </div>
                                <select name="kd_pelanggan" x-model="kd_pelanggan"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <option value="">Umum (Tanpa Pelanggan)</option>
                                    <template x-for="p in pelanggans" :key="p.kd_pelanggan">
                                        <option :value="p.kd_pelanggan" x-text="p.nm_pelanggan"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Diskon -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Diskon (%)</label>
                                <div class="relative mt-1">
                                    <input type="number" x-model.number="diskonInput" name="diskon" min="0"
                                        max="100"
                                        class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                                </div>
                            </div>

                            <!-- Keranjang -->
                            <div class="mt-6" x-show="cart.length > 0">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 uppercase">Keranjang Belanja</h4>
                                <div class="overflow-y-auto max-h-80">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                            <div class="flex-1 min-w-0 pr-3">
                                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.nm_obat"></p>
                                                <p class="text-xs text-gray-500"
                                                    x-text="'Rp ' + Number(item.harga_jual).toLocaleString('id-ID') + ' x ' + item.qty"></p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <div class="flex items-center border border-gray-300 rounded-lg">
                                                    <button type="button" @click="ubahQty(index, -1)"
                                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100">-</button>
                                                    <span class="w-8 text-center text-sm" x-text="item.qty"></span>
                                                    <button type="button" @click="ubahQty(index, 1)"
                                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100">+</button>
                                                </div>
                                                <button type="button" @click="hapus(index)"
                                                    class="text-red-500 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="mt-6 space-y-2 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Subtotal</span>
                                    <span class="text-sm font-semibold text-gray-900"
                                        x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Diskon</span>
                                    <span class="text-sm font-semibold text-red-600"
                                        x-text="'-Rp ' + diskon.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                                    <span class="text-base font-bold text-teal-600"
                                        x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <button type="button" @click="showPaymentModal = true" x-show="cart.length > 0"
                                class="inline-flex items-center justify-center w-full px-4 py-3 mt-6 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Lanjut ke Pembayaran
                            </button>

                            <p x-show="cart.length === 0" class="mt-6 text-sm text-center text-gray-500">
                                Tambahkan obat ke keranjang terlebih dahulu
                            </p>
                        </form>
                    </div>

                    <!-- Tab Content: Riwayat -->
                    <div x-show="activeTab === 'riwayat'" class="overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-emerald-50">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
                            <p class="text-xs text-gray-600 mt-1">50 transaksi terakhir</p>
                        </div>
                        <div class="overflow-y-auto" style="max-height: calc(100vh - 250px);">
                            @forelse ($penjualans as $penjualan)
                                <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <p class="text-xs font-semibold text-teal-800 bg-teal-100 inline-block px-2 py-0.5 rounded-full mb-1">
                                                {{ $penjualan->nota }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $penjualan->tgl_nota->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Pelanggan:</span>
                                            <span class="font-medium text-gray-900">{{ $penjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total:</span>
                                            <span class="text-gray-900">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between pt-1 border-t border-gray-200">
                                            <span class="text-gray-900 font-semibold">Grand Total:</span>
                                            <span class="font-bold text-teal-600">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Kasir:</span>
                                            <span class="text-gray-600">{{ $penjualan->user->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-gray-900">Belum ada transaksi</p>
                                    <p class="mt-1 text-xs text-gray-500">Mulai dengan membuat transaksi baru</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Pembayaran -->
        <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPaymentModal" @click="showPaymentModal = false"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPaymentModal" x-transition
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-5 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pembayaran</h3>
                            </div>
                            <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Ringkasan Transaksi -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Transaksi</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jumlah Item:</span>
                                    <span class="font-medium text-gray-900" x-text="cart.length + ' item'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium text-gray-900" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Diskon:</span>
                                    <span class="font-medium text-red-600" x-text="'-Rp ' + diskon.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-300">
                                    <span class="font-bold text-gray-900">Total Bayar:</span>
                                    <span class="text-xl font-bold text-teal-600" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Metode Pembayaran -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Pilih Metode Pembayaran</h4>
                            <div class="space-y-3">
                                <!-- Cash -->
                                <button type="button" @click="metodePembayaran = 'cash'"
                                    :class="metodePembayaran === 'cash' ? 'bg-teal-50 border-teal-500 ring-2 ring-teal-500' : 'border-gray-300 hover:bg-gray-50'"
                                    class="w-full flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all">
                                    <div class="flex items-center flex-1">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mr-4">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-base font-semibold text-gray-900">Tunai (Cash)</p>
                                            <p class="text-xs text-gray-500">Bayar dengan uang tunai</p>
                                        </div>
                                    </div>
                                    <svg x-show="metodePembayaran === 'cash'" class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <!-- QRIS -->
                                @if($qrisEnabled && $qrisImage)
                                <button type="button" @click="metodePembayaran = 'qris'"
                                    :class="metodePembayaran === 'qris' ? 'bg-teal-50 border-teal-500 ring-2 ring-teal-500' : 'border-gray-300 hover:bg-gray-50'"
                                    class="w-full flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all">
                                    <div class="flex items-center flex-1">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mr-4">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-base font-semibold text-gray-900">QRIS</p>
                                            <p class="text-xs text-gray-500">Scan QR Code untuk bayar</p>
                                        </div>
                                    </div>
                                    <svg x-show="metodePembayaran === 'qris'" class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>

                        <!-- QR Code Display (jika QRIS dipilih) -->
                        <div x-show="metodePembayaran === 'qris'" x-transition class="mb-6">
                            <div class="p-4 bg-white border-2 border-teal-500 rounded-lg">
                                <div class="flex flex-col items-center">
                                    <p class="text-sm font-semibold text-gray-700 mb-3">Scan QR Code di bawah ini:</p>
                                    <img src="{{ asset('storage/' . ($qrisImage ?? '')) }}" 
                                        alt="QRIS QR Code" 
                                        class="w-64 h-64 object-contain border-4 border-gray-200 rounded-lg">
                                    <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg w-full">
                                        <p class="text-xs text-blue-800 text-center">
                                            <span class="font-semibold">Petunjuk:</span> Scan QR menggunakan aplikasi pembayaran digital Anda
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showPaymentModal = false"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </button>
                            <button type="button" @click="prosesTransaksi" :disabled="!metodePembayaran || submitting"
                                :class="(metodePembayaran && !submitting) ? 'bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="inline-flex items-center px-6 py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg">
                                <svg x-show="!submitting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg x-show="submitting" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Memproses...' : 'Konfirmasi & Simpan'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('last_nota'))
            @php
                $lastNota = session('last_nota');
                $lastPenjualan = \App\Models\Penjualan::with('pelanggan')->find($lastNota);
            @endphp
            <!-- Modal Cetak Struk (muncul otomatis setelah transaksi berhasil) -->
            <div x-data="{ showStrukModal: true }" x-show="showStrukModal" x-cloak
                class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showStrukModal" @click="showStrukModal = false"
                        class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showStrukModal" x-transition
                        class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="px-6 py-5 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 mr-3 bg-green-100 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Transaksi Berhasil</h3>
                                        <p class="text-xs text-gray-600">Cetak struk untuk pelanggan?</p>
                                    </div>
                                </div>
                                <button type="button" @click="showStrukModal = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Ringkasan Nota -->
                            <div class="p-4 mb-5 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="space-y-1.5 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">No. Nota:</span>
                                        <span class="font-mono font-semibold text-teal-700">{{ $lastNota }}</span>
                                    </div>
                                    @if ($lastPenjualan)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Pelanggan:</span>
                                            <span class="font-medium text-gray-900">{{ $lastPenjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Pembayaran:</span>
                                            <span class="font-medium text-gray-900 uppercase">{{ $lastPenjualan->metode_pembayaran }}</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-300">
                                            <span class="font-bold text-gray-900">Grand Total:</span>
                                            <span class="text-lg font-bold text-teal-600">Rp {{ number_format($lastPenjualan->grand_total, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Pilihan Cetak -->
                            <p class="mb-3 text-sm font-semibold text-gray-700">Pilih Format Struk</p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <a href="{{ route('karyawan.riwayat-transaksi.cetak-thermal', $lastNota) }}"
                                    target="_blank" rel="noopener"
                                    class="flex flex-col items-center justify-center p-4 text-center transition-all border-2 border-gray-300 rounded-lg hover:border-teal-500 hover:bg-teal-50 group">
                                    <svg class="w-8 h-8 mb-2 text-gray-600 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-700">Struk Thermal</span>
                                    <span class="text-xs text-gray-500">Printer 58/80mm</span>
                                </a>
                                <a href="{{ route('karyawan.riwayat-transaksi.cetak-pdf', $lastNota) }}"
                                    target="_blank" rel="noopener"
                                    class="flex flex-col items-center justify-center p-4 text-center transition-all border-2 border-gray-300 rounded-lg hover:border-teal-500 hover:bg-teal-50 group">
                                    <svg class="w-8 h-8 mb-2 text-gray-600 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-700">Struk PDF</span>
                                    <span class="text-xs text-gray-500">Kertas A5 / Print biasa</span>
                                </a>
                            </div>

                            <!-- Action -->
                            <div class="flex items-center justify-end mt-6">
                                <button type="button" @click="showStrukModal = false"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Lewati
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Pelanggan Baru -->
        <div x-show="showPelangganBaru" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPelangganBaru" @click="showPelangganBaru = false"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPelangganBaru" x-transition
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="px-6 py-5 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900">Tambah Pelanggan Baru</h3>
                            <button type="button" @click="showPelangganBaru = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200 rounded-lg px-3 py-2">
                            Kode pelanggan dibuat otomatis oleh sistem.
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Nama Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="pelangganBaru.nm_pelanggan" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                placeholder="Contoh: Budi Santoso">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telepon</label>
                            <input type="text" x-model="pelangganBaru.telpon"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                placeholder="Contoh: 081234567890">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota</label>
                            <input type="text" x-model="pelangganBaru.kota"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                placeholder="Contoh: Bandung">
                        </div>

                        <p x-show="errorPelanggan" x-text="errorPelanggan"
                            class="text-sm text-red-600"></p>

                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" @click="showPelangganBaru = false"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="button" @click="simpanPelangganBaru"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-lg">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Pelanggan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.obats = @json($obats);
        window.pelanggansData = @json($pelanggans);
        
        function transaksiData() {
            return {
                cart: [],
                pencarian: '',
                kd_pelanggan: '',
                diskonInput: 0,
                metodePembayaran: '',
                submitting: false,
                showPelangganBaru: false,
                showPaymentModal: false,
                pelangganBaru: { nm_pelanggan: '', telpon: '', kota: '' },
                errorPelanggan: '',
                notif: '',
                pelanggans: window.pelanggansData,
                
                get filteredObats() {
                    const q = this.pencarian.toLowerCase();
                    return window.obats.filter(o => {
                        return !q ||
                            o.nm_obat.toLowerCase().includes(q) ||
                            (o.kd_obat || '').toLowerCase().includes(q) ||
                            ((o.jenis || '').toLowerCase().includes(q));
                    });
                },
                
                get total() {
                    return this.cart.reduce((sum, item) => sum + (parseFloat(item.harga_jual) || 0) * item.qty, 0);
                },
                
                get diskon() {
                    return (this.total * parseFloat(this.diskonInput || 0)) / 100;
                },
                
                get grandTotal() {
                    return this.total - this.diskon;
                },
                
                tampilkanNotif(pesan) {
                    this.notif = pesan;
                    clearTimeout(this._notifTimer);
                    this._notifTimer = setTimeout(() => { this.notif = ''; }, 2500);
                },
                
                tambah(obat) {
                    const existing = this.cart.find(i => i.kd_obat === obat.kd_obat);
                    const qtySekarang = existing ? existing.qty : 0;
                    if (qtySekarang >= obat.stok) {
                        this.tampilkanNotif('Stok "' + obat.nm_obat + '" tidak mencukupi!');
                        return;
                    }
                    if (existing) {
                        existing.qty += 1;
                    } else {
                        this.cart.push({
                            kd_obat: obat.kd_obat,
                            nm_obat: obat.nm_obat,
                            satuan: obat.satuan || '',
                            harga_jual: parseFloat(obat.harga_jual) || 0,
                            stok: obat.stok,
                            qty: 1
                        });
                    }
                },
                
                ubahQty(index, delta) {
                    const item = this.cart[index];
                    const next = (parseInt(item.qty) || 1) + delta;
                    if (next > item.stok) {
                        this.tampilkanNotif('Stok "' + item.nm_obat + '" tidak mencukupi!');
                        return;
                    }
                    item.qty = Math.max(1, next);
                },
                
                hapus(index) {
                    this.cart.splice(index, 1);
                },
                
                prosesTransaksi() {
                    // Validasi cart & metode pembayaran
                    if (this.cart.length === 0) {
                        this.tampilkanNotif('Keranjang masih kosong!');
                        return;
                    }
                    if (!this.metodePembayaran) {
                        this.tampilkanNotif('Pilih metode pembayaran terlebih dahulu!');
                        return;
                    }
                    if (this.submitting) return;
                    this.submitting = true;

                    // Submit form (metode_pembayaran & items sudah ter-bind lewat hidden input Alpine)
                    const form = document.querySelector('form[action="{{ route('karyawan.transaksi.store') }}"]');
                    if (!form) {
                        this.submitting = false;
                        this.tampilkanNotif('Form transaksi tidak ditemukan.');
                        return;
                    }
                    form.submit();
                },
                
                async simpanPelangganBaru() {
                    this.errorPelanggan = '';
                    const fd = new FormData();
                    fd.append('nm_pelanggan', this.pelangganBaru.nm_pelanggan);
                    fd.append('telpon', this.pelangganBaru.telpon || '');
                    fd.append('kota', this.pelangganBaru.kota || '');

                    try {
                        const res = await fetch('{{ route('karyawan.pelanggan.quick') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: fd
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.pelanggans.push(data.pelanggan);
                            this.kd_pelanggan = data.pelanggan.kd_pelanggan;
                            this.showPelangganBaru = false;
                            this.pelangganBaru = { nm_pelanggan: '', telpon: '', kota: '' };
                            this.tampilkanNotif('Pelanggan "' + data.pelanggan.nm_pelanggan + '" ditambahkan!');
                        } else {
                            const err = await res.json().catch(() => ({}));
                            const key = Object.keys(err.errors || {})[0];
                            this.errorPelanggan = key ? err.errors[key][0] : 'Gagal menambahkan pelanggan';
                        }
                    } catch (e) {
                        this.errorPelanggan = 'Terjadi kesalahan, coba lagi';
                    }
                }
            };
        }
    </script>
@endpush