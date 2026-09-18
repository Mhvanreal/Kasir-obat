@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-6xl sm:px-6 lg:px-8" x-data="{ qrisEnabled: {{ $qrisEnabled == '1' ? 'true' : 'false' }} }">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div
                    class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pengaturan Sistem</h1>
                    <p class="text-sm text-gray-600">Kelola pengaturan aplikasi dan metode pembayaran</p>
                </div>
            </div>
        </div>

        {{-- Flash success/error ditampilkan otomatis lewat <x-flash-toast /> di layouts.app --}}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Pengaturan QRIS -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-900">Pembayaran QRIS</h3>
                    <p class="text-sm text-gray-600">Upload QR Code untuk pembayaran QRIS</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.pengaturan.qris') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Status QRIS -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Status QRIS <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="qris_enabled" value="1" 
                                        {{ $qrisEnabled == '1' ? 'checked' : '' }}
                                        x-model="qrisEnabled"
                                        @change="qrisEnabled = true"
                                        class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Aktif</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="qris_enabled" value="0" 
                                        {{ $qrisEnabled == '0' ? 'checked' : '' }}
                                        x-model="qrisEnabled"
                                        @change="qrisEnabled = false"
                                        class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Nonaktif</span>
                                </label>
                            </div>
                            @error('qris_enabled')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alert jika status nonaktif -->
                        <div x-show="!qrisEnabled" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-semibold mb-1">Status QRIS Nonaktif</p>
                                    <p>Aktifkan status QRIS terlebih dahulu untuk dapat mengupload QR Code.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current QR Code -->
                        @if ($qrisImage)
                            <div class="mb-4" x-show="qrisEnabled">
                                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code Saat Ini</label>
                                <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <img src="{{ asset('storage/' . $qrisImage) }}" 
                                        alt="QRIS QR Code" 
                                        class="w-64 h-64 object-contain">
                                </div>
                            </div>
                        @endif

                        <!-- Upload QR Code -->
                        <div class="mb-4" x-show="qrisEnabled">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $qrisImage ? 'Ganti QR Code' : 'Upload QR Code' }}
                                @if(!$qrisImage)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="file" name="qris_image" accept="image/*"
                                {{ !$qrisImage ? 'required' : '' }}
                                :disabled="!qrisEnabled"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB. Wajib diisi jika status aktif dan belum ada QR Code.</p>
                            @error('qris_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Petunjuk -->
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg" x-show="qrisEnabled">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Petunjuk Upload QR QRIS:</p>
                                    <ol class="list-decimal ml-4 space-y-1">
                                        <li>Aktifkan status QRIS terlebih dahulu</li>
                                        <li>Dapatkan QR Code QRIS dari bank atau payment gateway Anda</li>
                                        <li>Screenshot atau download QR Code tersebut</li>
                                        <li>Upload file gambar di form ini</li>
                                        <li>QR Code akan muncul sebagai opsi pembayaran di transaksi</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pengaturan QRIS
                        </button>
                    </form>
                </div>
            </div>

            <!-- Pengaturan Umum -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-900">Pengaturan Umum</h3>
                    <p class="text-sm text-gray-600">Informasi dasar aplikasi</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.pengaturan.general') }}" method="POST">
                        @csrf

                        <!-- Nama Toko -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Toko/Apotek <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_toko" value="{{ old('nama_toko', $namaToko) }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                placeholder="Contoh: Apotek Citra">
                            @error('nama_toko')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Catatan:</span> Nama toko akan ditampilkan di nota transaksi dan laporan.
                            </p>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pengaturan Umum
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="mt-6 overflow-hidden bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Sistem</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Versi Aplikasi</p>
                        <p class="text-xl font-bold text-gray-900">v1.0.0</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Framework</p>
                        <p class="text-xl font-bold text-gray-900">Laravel {{ app()->version() }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Status QRIS</p>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2 {{ $qrisEnabled == '1' ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                            <p class="text-xl font-bold {{ $qrisEnabled == '1' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $qrisEnabled == '1' ? 'Aktif' : 'Nonaktif' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
