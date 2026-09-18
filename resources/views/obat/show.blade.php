@extends('layouts.app')

@section('title', 'Detail Obat')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-6xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('karyawan.obat.index') }}"
                    class="inline-flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Obat</h1>
                    <p class="text-sm text-gray-600">Informasi lengkap obat {{ $obat->nm_obat }}</p>
                </div>
                <a href="{{ route('karyawan.obat.edit', $obat->kd_obat) }}"
                    class="ml-auto inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-lg">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Obat
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Image -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="h-64 bg-gray-100">
                    <x-obat-image :obat="$obat" class="object-cover w-full h-full" />
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">
                            {{ $obat->kd_obat }}
                        </span>
                        @if ($obat->stok < 10)
                            <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                Stok Menipis
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Stok Tersedia
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $obat->nm_obat }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $obat->jenis }} · {{ $obat->satuan }}</p>
                </div>
            </div>

            <!-- Informasi Detail -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Lengkap</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Kode Obat</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $obat->kd_obat }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Nama Obat</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $obat->nm_obat }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Jenis</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $obat->jenis }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Satuan</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $obat->satuan }}</dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Harga Beli</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">
                                Rp {{ number_format($obat->harga_beli, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
                            <dd class="mt-1 text-base font-semibold text-teal-600">
                                Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Stok Tersedia</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">
                                {{ $obat->stok }} {{ $obat->satuan }}
                            </dd>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900">
                                @if ($obat->supplier)
                                    {{ $obat->supplier->nm_supplier }}
                                    <span class="text-sm font-normal text-gray-500">
                                        ({{ $obat->supplier->kota ?: '-' }})
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection