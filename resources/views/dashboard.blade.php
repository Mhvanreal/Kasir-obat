@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Selamat datang, {{ Auth::user()->name }}</h1>
                    <p class="text-sm text-gray-600">
                        Role:
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                            @if (Auth::user()->role === 'admin') bg-red-100 text-red-800
                            @elseif(Auth::user()->role === 'owner') bg-purple-100 text-purple-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ strtoupper(Auth::user()->role) }}
                        </span>
                        <span class="mx-1 text-gray-300">·</span>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                    @if ($isKaryawan)
                        <p class="mt-2 inline-flex items-center gap-1 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 00.99-1v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Data yang ditampilkan hanya penjualan Anda sendiri
                        </p>
                    @endif
                </div>
            </div>

            <!-- Range Preset -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-600">Rentang</label>
                <select name="range" onchange="this.form.submit()"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @foreach ($rangePresets as $key => $preset)
                        <option value="{{ $key }}" @selected($rangeKey === $key)>{{ $preset['label'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Penjualan Hari Ini -->
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-teal-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">
                            {{ $isKaryawan ? 'Penjualan Anda Hari Ini' : 'Penjualan Hari Ini' }}
                        </p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $transaksiHariIni }} transaksi</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-teal-100">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8v8m0 0v.5m0-8.5V4m0 4a3 3 0 100 6m0 0v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Penjualan Bulan Ini -->
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">
                            {{ $isKaryawan ? 'Penjualan Anda Bulan Ini' : 'Penjualan Bulan Ini' }}
                        </p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($penjualanBulanIni, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            @if (Auth::user()->isAdmin() || Auth::user()->isOwner())
                <!-- Pembelian Bulan Ini -->
                <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Pembelian Bulan Ini</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($pembelianBulanIni, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-gray-500">Restok dari supplier</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            @php $totalPerhatianStok = $obatHabis + $obatStokKritis + $obatStokRendah; @endphp
            <!-- Stok Perhatian -->
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4
                {{ ($obatHabis + $obatStokKritis) > 0 ? 'border-red-500' : ($obatStokRendah > 0 ? 'border-yellow-500' : 'border-gray-300') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Perhatian Stok</p>
                        <p class="mt-2 text-2xl font-bold {{ ($obatHabis + $obatStokKritis) > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $totalPerhatianStok }}
                        </p>
                        <div class="mt-1 flex flex-wrap gap-x-2 text-xs">
                            @if ($obatHabis > 0)
                                <span class="text-red-700 font-semibold">{{ $obatHabis }} habis</span>
                            @endif
                            @if ($obatStokKritis > 0)
                                <span class="text-red-600 font-semibold">{{ $obatStokKritis }} kritis</span>
                            @endif
                            @if ($obatStokRendah > 0)
                                <span class="text-yellow-700 font-semibold">{{ $obatStokRendah }} rendah</span>
                            @endif
                            @if ($totalPerhatianStok === 0)
                                <span class="text-gray-500">Semua stok aman</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-full
                        {{ ($obatHabis + $obatStokKritis) > 0 ? 'bg-red-100' : ($obatStokRendah > 0 ? 'bg-yellow-100' : 'bg-gray-100') }}">
                        <svg class="w-6 h-6
                            {{ ($obatHabis + $obatStokKritis) > 0 ? 'text-red-600 animate-pulse' : ($obatStokRendah > 0 ? 'text-yellow-600' : 'text-gray-500') }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart + Metode Pembayaran -->
        <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
            <!-- Chart Trend -->
            <div class="lg:col-span-2 p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Grafik Penjualan</h3>
                        <p class="text-xs text-gray-500">{{ $rangeLabel }}</p>
                    </div>
                </div>
                <div class="relative h-72">
                    <canvas id="chartTrend"></canvas>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
                <p class="text-xs text-gray-500 -mt-3 mb-3">Bulan ini</p>
                @if ($metodeBreakdown->count() > 0)
                    <div class="relative h-56">
                        <canvas id="chartMetode"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ($metodeBreakdown as $item)
                            <div class="flex items-center justify-between text-sm">
                                <span class="uppercase font-medium text-gray-700">{{ $item->metode_pembayaran }}</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-56 text-gray-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="text-sm">Belum ada transaksi bulan ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Obat + Transaksi Terbaru -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Top Obat -->
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-1 text-lg font-semibold text-gray-900">Top 5 Obat Terlaris</h3>
                <p class="mb-4 text-xs text-gray-500">Berdasarkan jumlah terjual bulan ini</p>
                @if ($topObats->count() > 0)
                    <div class="space-y-3">
                        @foreach ($topObats as $index => $item)
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-teal-700 bg-teal-100 rounded-full">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->obat->nm_obat ?? $item->kd_obat }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <span>{{ number_format($item->total_terjual, 0, ',', '.') }} {{ $item->obat->satuan ?? 'unit' }}</span>
                                        <span>·</span>
                                        <span>Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-6">Belum ada penjualan bulan ini</p>
                @endif
            </div>

            <!-- Transaksi Terbaru -->
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
                        <p class="text-xs text-gray-500">8 transaksi terakhir</p>
                    </div>
                    @if (in_array(Auth::user()->role, ['admin', 'owner', 'karyawan']))
                        <a href="{{ route('karyawan.riwayat-transaksi.index') }}"
                            class="text-xs font-semibold text-teal-600 hover:text-teal-800">Lihat semua &rarr;</a>
                    @endif
                </div>
                @if ($recentPenjualans->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach ($recentPenjualans as $trx)
                            <div class="flex items-center justify-between py-2.5">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-mono font-semibold text-teal-700 truncate">{{ $trx->nota }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $trx->pelanggan->nm_pelanggan ?? 'Umum' }} · {{ $trx->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 uppercase">{{ $trx->metode_pembayaran }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-6">Belum ada transaksi</p>
                @endif
            </div>
        </div>

        {{-- Penjualan per Karyawan (hanya admin/owner) --}}
        @if (! $isKaryawan && $penjualanPerKaryawan->count() > 0)
            <div class="mt-6 p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Penjualan per Karyawan</h3>
                        <p class="text-xs text-gray-500">Rekap kontribusi tiap kasir bulan {{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    @php
                        $totalSemua = $penjualanPerKaryawan->sum('total_penjualan');
                    @endphp
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase">Total Gabungan</p>
                        <p class="text-lg font-bold text-teal-600">Rp {{ number_format($totalSemua, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Karyawan</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-right">Jumlah Transaksi</th>
                                <th class="px-4 py-3 text-right">Total Penjualan</th>
                                <th class="px-4 py-3 text-right">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($penjualanPerKaryawan as $k)
                                @php
                                    $persen = $totalSemua > 0 ? ($k->total_penjualan / $totalSemua) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white text-xs font-bold">
                                                {{ strtoupper(substr($k->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $k->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                            @if ($k->role === 'admin') bg-red-100 text-red-800
                                            @elseif($k->role === 'owner') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($k->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">
                                        {{ number_format($k->jumlah_transaksi, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 tabular-nums">
                                        Rp {{ number_format($k->total_penjualan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-500"
                                                    style="width: {{ number_format($persen, 1, '.', '') }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-600 w-12 text-right tabular-nums">
                                                {{ number_format($persen, 1) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const rupiah = v => 'Rp ' + Number(v).toLocaleString('id-ID');

            // Trend chart (line)
            const trendCtx = document.getElementById('chartTrend');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trendLabels),
                        datasets: [
                            {
                                label: 'Penjualan (Rp)',
                                data: @json($trendPenjualan),
                                borderColor: '#0d9488',
                                backgroundColor: 'rgba(13,148,136,0.12)',
                                borderWidth: 2,
                                tension: 0.35,
                                fill: true,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Jumlah Transaksi',
                                data: @json($trendTransaksi),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.1)',
                                borderWidth: 2,
                                tension: 0.35,
                                yAxisID: 'y1',
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
                                    label: function (ctx) {
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

            // Metode pembayaran (doughnut)
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
