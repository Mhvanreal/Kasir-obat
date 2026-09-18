<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan {{ $dari }} - {{ $sampai }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            padding: 20px 24px;
            font-size: 12px;
        }
        header { border-bottom: 2px solid #0d9488; padding-bottom: 12px; margin-bottom: 16px; }
        header h1 { font-size: 20px; color: #0d9488; }
        header .subtitle { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .meta { margin-top: 6px; font-size: 11px; color: #4b5563; }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin: 16px 0;
        }
        .kpi {
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-left: 3px solid #0d9488;
            border-radius: 4px;
        }
        .kpi .label { font-size: 9px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        .kpi .value { font-size: 14px; font-weight: 700; color: #111827; margin-top: 4px; }
        .kpi.negative .value { color: #dc2626; }

        h2 { font-size: 14px; margin: 16px 0 8px; color: #0f766e; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead { background: #f3f4f6; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { font-weight: 600; text-transform: uppercase; font-size: 9px; letter-spacing: 0.03em; color: #4b5563; }
        td.num, th.num { text-align: right; }
        td.center, th.center { text-align: center; }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 8px;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            background: #d1fae5;
            color: #065f46;
        }
        .badge.qris { background: #dbeafe; color: #1e40af; }

        footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            display: flex;
            justify-content: space-between;
        }

        .print-actions {
            position: fixed;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }
        .print-actions button, .print-actions a {
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .print-actions .btn-primary { background: #0d9488; color: #fff; }
        .print-actions .btn-primary:hover { background: #0f766e; }
        .print-actions .btn-secondary { background: #e5e7eb; color: #1f2937; }

        @media print {
            .print-actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button class="btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn-secondary" href="javascript:window.close()">Tutup</a>
    </div>

    <header>
        <h1>Laporan Penjualan</h1>
        <div class="subtitle">Apotek Citra &middot; Dicetak {{ now()->translatedFormat('l, d F Y H:i') }}</div>
        <div class="meta">
            Periode: <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }}
            &ndash; {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</strong>
            @if ($metode)
                &middot; Metode: <strong>{{ strtoupper($metode) }}</strong>
            @endif
        </div>
    </header>

    <!-- KPI -->
    <div class="kpi-grid">
        <div class="kpi">
            <div class="label">Total Penjualan</div>
            <div class="value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        </div>
        <div class="kpi">
            <div class="label">Jumlah Transaksi</div>
            <div class="value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
        </div>
        <div class="kpi">
            <div class="label">Rata-rata</div>
            <div class="value">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
        </div>
        <div class="kpi">
            <div class="label">Total Diskon</div>
            <div class="value">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
        </div>
        <div class="kpi {{ $keuntunganEstimasi < 0 ? 'negative' : '' }}">
            <div class="label">Estimasi Keuntungan</div>
            <div class="value">Rp {{ number_format($keuntunganEstimasi, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Breakdown -->
    <div class="two-col">
        <div>
            <h2>Distribusi Metode Pembayaran</h2>
            <table>
                <thead>
                    <tr>
                        <th>Metode</th>
                        <th class="num">Transaksi</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($metodeBreakdown as $m)
                        <tr>
                            <td>
                                <span class="badge {{ $m->metode_pembayaran === 'qris' ? 'qris' : '' }}">
                                    {{ strtoupper($m->metode_pembayaran) }}
                                </span>
                            </td>
                            <td class="num">{{ $m->jumlah }}</td>
                            <td class="num">Rp {{ number_format($m->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="center">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <h2>Top 10 Obat Terlaris</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Obat</th>
                        <th class="num">Terjual</th>
                        <th class="num">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topObats as $idx => $t)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td>{{ $t->obat->nm_obat ?? $t->kd_obat }}</td>
                            <td class="num">{{ number_format($t->total_terjual, 0, ',', '.') }} {{ $t->obat->satuan ?? '' }}</td>
                            <td class="num">Rp {{ number_format($t->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="center">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daftar Transaksi -->
    <h2>Daftar Transaksi ({{ $penjualans->count() }} transaksi)</h2>
    <table>
        <thead>
            <tr>
                <th>Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="num">Total</th>
                <th class="num">Diskon</th>
                <th class="num">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penjualans as $p)
                <tr>
                    <td style="font-family: monospace; font-size: 10px;">{{ $p->nota }}</td>
                    <td>{{ $p->tgl_nota->translatedFormat('d M Y') }}</td>
                    <td>{{ $p->pelanggan->nm_pelanggan ?? 'Umum' }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $p->metode_pembayaran === 'qris' ? 'qris' : '' }}">
                            {{ strtoupper($p->metode_pembayaran) }}
                        </span>
                    </td>
                    <td class="num">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($p->diskon, 0, ',', '.') }}%</td>
                    <td class="num"><strong>Rp {{ number_format($p->grand_total, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="8" class="center">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if ($penjualans->count() > 0)
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align: right;">TOTAL PENJUALAN</th>
                    <th class="num" style="color: #0d9488;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        @endif
    </table>

    <footer>
        <span>Apotek Citra &middot; Sistem Kasir Obat</span>
        <span>Halaman 1 dari 1</span>
    </footer>

    <script>
        // Auto-open print dialog kalau URL query ada ?autoprint=1
        if (new URLSearchParams(location.search).get('autoprint') === '1') {
            window.addEventListener('load', () => setTimeout(() => window.print(), 300));
        }
    </script>
</body>
</html>
