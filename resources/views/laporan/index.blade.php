@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8">
        <!-- Header -->
        @php
            $exportQuery = array_filter([
                'range' => $rangeKey === 'custom' ? null : $rangeKey,
                'tanggal_dari' => $rangeKey === 'custom' ? $dari : null,
                'tanggal_sampai' => $rangeKey === 'custom' ? $sampai : null,
                'periode' => $periode,
                'metode_pembayaran' => $metode,
            ], fn($v) => $v !== null && $v !== '');
        @endphp
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Penjualan</h1>
                    <p class="text-sm text-gray-600">
                        Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }}
                        &ndash; {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.export', $exportQuery) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Unduh CSV
                </a>
                <a href="{{ route('admin.laporan.cetak', $exportQuery) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / PDF
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="" class="p-4 mb-6 bg-white rounded-lg shadow-sm">
            <!-- Preset buttons -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-semibold text-gray-600 mr-1">Preset:</span>
                @foreach ($rangePresets as $key => $label)
                    @if ($key !== 'custom')
                        <a href="{{ url()->current() }}?range={{ $key }}&periode={{ $periode }}@if($metode)&metode_pembayaran={{ $metode }}@endif"
                            class="px-3 py-1 text-xs font-medium rounded-full transition-colors
                            {{ $rangeKey === $key ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- Detailed filter -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ $dari }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ $sampai }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Grafik</label>
                    <select name="periode"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        @foreach ($periodeOptions as $key => $label)
                            <option value="{{ $key }}" @selected($periode === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua</option>
                        <option value="cash" @selected($metode === 'cash')>Cash</option>
                        <option value="qris" @selected($metode === 'qris')>QRIS</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- KPI Ringkasan -->
        <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-5">
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Penjualan</p>
                <p class="mt-2 text-xl font-bold text-teal-600">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Jumlah Transaksi</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Rata-rata Transaksi</p>
                <p class="mt-2 text-xl font-bold text-blue-600">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Diskon</p>
                <p class="mt-2 text-xl font-bold text-red-600">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Estimasi Keuntungan</p>
                <p class="mt-2 text-xl font-bold {{ $keuntunganEstimasi >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($keuntunganEstimasi, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-gray-400 mt-1">*berdasarkan harga beli terkini</p>
            </div>
        </div>

        <!-- Chart Utama + Metode Pembayaran -->
        <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
            <div class="lg:col-span-2 p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-1 text-lg font-semibold text-gray-900">Grafik Penjualan</h3>
                <p class="mb-4 text-xs text-gray-500">Agregasi {{ strtolower($periodeOptions[$periode]) }}</p>
                <div class="relative h-72">
                    <canvas id="chartLaporan"></canvas>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-1 text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
                <p class="mb-4 text-xs text-gray-500">Distribusi transaksi</p>
                @if ($metodeBreakdown->count() > 0)
                    <div class="relative h-56">
                        <canvas id="chartMetode"></canvas>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        @foreach ($metodeBreakdown as $item)
                            <div class="flex items-center justify-between">
                                <span class="uppercase font-medium text-gray-700">{{ $item->metode_pembayaran }}</span>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->jumlah }} transaksi</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-10">Tidak ada data</p>
                @endif
            </div>
        </div>

        <!-- Top Obat + Tabel Transaksi -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Top Obat -->
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-1 text-lg font-semibold text-gray-900">Top 10 Obat Terlaris</h3>
                <p class="mb-4 text-xs text-gray-500">Dalam periode terpilih</p>
                @if ($topObats->count() > 0)
                    <div class="space-y-3">
                        @foreach ($topObats as $index => $item)
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-7 h-7 text-xs font-bold text-white rounded-full
                                    {{ $index < 3 ? 'bg-teal-600' : 'bg-gray-400' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->obat->nm_obat ?? $item->kd_obat }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ number_format($item->total_terjual, 0, ',', '.') }} {{ $item->obat->satuan ?? 'unit' }}</span>
                                        <span class="font-semibold text-teal-600">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-10">Tidak ada data</p>
                @endif
            </div>

            <!-- Tabel Transaksi -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
                    <p class="text-xs text-gray-500">{{ $penjualans->total() }} transaksi ditemukan</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Nota</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Pelanggan</th>
                                <th class="px-4 py-3 text-left">Metode</th>
                                <th class="px-4 py-3 text-right">Grand Total</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($penjualans as $penjualan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs font-semibold text-teal-700">{{ $penjualan->nota }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $penjualan->tgl_nota->translatedFormat('d M Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $penjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</td>
                                    <td class="px-4 py-3 uppercase text-xs">
                                        <span class="px-2 py-0.5 rounded-full font-medium
                                            {{ $penjualan->metode_pembayaran === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $penjualan->metode_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('karyawan.riwayat-transaksi.show', $penjualan->nota) }}"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-teal-700 bg-teal-50 rounded hover:bg-teal-100">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($penjualans->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $penjualans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const rupiah = v => 'Rp ' + Number(v).toLocaleString('id-ID');

            const laporanCtx = document.getElementById('chartLaporan');
            if (laporanCtx) {
                new Chart(laporanCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Penjualan (Rp)',
                                data: @json($chartPenjualan),
                                backgroundColor: 'rgba(13,148,136,0.75)',
                                borderColor: '#0d9488',
                                borderWidth: 1,
                                yAxisID: 'y',
                                order: 2,
                            },
                            {
                                type: 'line',
                                label: 'Jumlah Transaksi',
                                data: @json($chartTransaksi),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.2)',
                                borderWidth: 2,
                                tension: 0.35,
                                yAxisID: 'y1',
                                order: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        if (ctx.dataset.yAxisID === 'y') {
                                            return ctx.dataset.label + ': ' + rupiah(ctx.parsed.y);
                                        }
                                        return ctx.dataset.label + ': ' + ctx.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                ticks: { callback: v => rupiah(v) }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }

            const metodeCtx = document.getElementById('chartMetode');
            if (metodeCtx) {
                const rows = @json($metodeBreakdown);
                new Chart(metodeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: rows.map(r => r.metode_pembayaran.toUpperCase()),
                        datasets: [{
                            data: rows.map(r => parseFloat(r.total)),
                            backgroundColor: ['#0d9488', '#3b82f6', '#f59e0b', '#8b5cf6'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.label + ': ' + rupiah(ctx.parsed)
                                }
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endpush
