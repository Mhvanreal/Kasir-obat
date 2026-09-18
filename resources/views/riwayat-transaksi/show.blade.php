@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-6xl sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('karyawan.riwayat-transaksi.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Cetak PDF -->
                    <a href="{{ route('karyawan.riwayat-transaksi.cetak-pdf', $penjualan->nota) }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Cetak PDF
                    </a>
                    <!-- Cetak Thermal -->
                    <a href="{{ route('karyawan.riwayat-transaksi.cetak-thermal', $penjualan->nota) }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-teal-600 to-emerald-600 rounded-lg hover:from-teal-700 hover:to-emerald-700 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Struk
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left: Transaction Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Transaksi -->
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Transaksi</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">No. Nota</p>
                                <p class="text-base font-semibold text-gray-900">{{ $penjualan->nota }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal & Waktu</p>
                                <p class="text-base font-semibold text-gray-900">{{ $penjualan->tgl_nota->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kasir</p>
                                <p class="text-base font-semibold text-gray-900">{{ $penjualan->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pelanggan</p>
                                <p class="text-base font-semibold text-gray-900">{{ $penjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Metode Pembayaran</p>
                                <div class="mt-1">
                                    @if($penjualan->metode_pembayaran == 'cash')
                                        <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full uppercase">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Cash
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full uppercase">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                            QRIS
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Transaksi -->
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <h3 class="text-lg font-semibold text-gray-900">Item Pembelian</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Obat</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Harga</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($penjualan->details as $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @if($detail->obat && $detail->obat->gambar)
                                                    <img src="{{ asset('storage/' . $detail->obat->gambar) }}" 
                                                        alt="{{ $detail->obat->nm_obat }}"
                                                        class="w-12 h-12 object-cover rounded-lg mr-3 border border-gray-200">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 rounded-lg mr-3 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $detail->obat->nm_obat ?? 'Obat tidak ditemukan' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $detail->obat->jenis ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-sm font-semibold text-teal-800 bg-teal-100 rounded-full">
                                                {{ $detail->jumlah }} {{ $detail->obat->satuan ?? 'pcs' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 text-right whitespace-nowrap">
                                            Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                            Rp {{ number_format($detail->harga_jual * $detail->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pembayaran</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Total Items -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Total Item</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $penjualan->details->count() }} item</span>
                        </div>

                        <!-- Subtotal -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
                        </div>

                        <!-- Diskon -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Diskon ({{ $penjualan->diskon }}%)</span>
                            <span class="text-sm font-semibold text-red-600">
                                -Rp {{ number_format($penjualan->total * $penjualan->diskon / 100, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Grand Total -->
                        <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                            <span class="text-base font-bold text-gray-900">Grand Total</span>
                            <span class="text-xl font-bold text-teal-600">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Informasi Cetak</p>
                                    <ul class="list-disc ml-4 space-y-1 text-xs">
                                        <li><strong>PDF:</strong> Struk formal untuk arsip</li>
                                        <li><strong>Thermal:</strong> Struk kasir 58/80mm</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="mt-6 pt-4 border-t border-gray-200 space-y-2 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Dibuat:</span>
                                <span>{{ $penjualan->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($penjualan->updated_at != $penjualan->created_at)
                                <div class="flex justify-between">
                                    <span>Diupdate:</span>
                                    <span>{{ $penjualan->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
