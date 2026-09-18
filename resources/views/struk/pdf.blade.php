<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $penjualan->nota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #0d9488;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 28px;
            color: #0d9488;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .header .contact {
            font-size: 11px;
            color: #888;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-right {
            text-align: right;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }

        .info-value {
            color: #333;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-cash {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-qris {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background-color: #0d9488;
            color: white;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .items-table td {
            padding: 10px;
            font-size: 11px;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .item-type {
            font-size: 10px;
            color: #888;
        }

        /* Summary */
        .summary {
            float: right;
            width: 300px;
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-row.total {
            border-top: 2px solid #0d9488;
            border-bottom: 2px solid #0d9488;
            margin-top: 5px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .summary-label {
            font-weight: 600;
            color: #555;
        }

        .summary-value {
            font-weight: bold;
            color: #333;
        }

        .summary-row.total .summary-label {
            font-size: 14px;
            color: #0d9488;
        }

        .summary-row.total .summary-value {
            font-size: 16px;
            color: #0d9488;
        }

        .summary-row.discount .summary-value {
            color: #dc2626;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #0d9488;
            text-align: center;
        }

        .footer .thank-you {
            font-size: 16px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 10px;
        }

        .footer .notes {
            font-size: 10px;
            color: #888;
            margin-top: 10px;
            line-height: 1.6;
        }

        .footer .signature {
            margin-top: 40px;
            text-align: right;
            padding-right: 50px;
        }

        .footer .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 60px;
            margin-left: auto;
            padding-top: 5px;
            font-size: 11px;
            color: #666;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
            }

            .container {
                max-width: 100%;
            }

            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>APOTEK CITRA</h1>
            <div class="subtitle">Struk Transaksi Penjualan</div>
            <div class="contact">
                Jl. Contoh No. 123, Jakarta | Telp: (021) 1234567 | Email: info@apotekcitra.com
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-row">
                    <span class="info-label">No. Nota</span>
                    <span class="info-value">: {{ $penjualan->nota }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">: {{ $penjualan->tgl_nota->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Waktu</span>
                    <span class="info-value">: {{ $penjualan->tgl_nota->format('H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-value">: {{ $penjualan->pelanggan->nm_pelanggan ?? 'Umum' }}</span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-row">
                    <span class="info-label">Kasir</span>
                    <span class="info-value">: {{ $penjualan->user->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Pembayaran</span>
                    <span class="info-value">: 
                        @if($penjualan->metode_pembayaran == 'cash')
                            <span class="badge badge-cash">Cash</span>
                        @else
                            <span class="badge badge-qris">QRIS</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Nama Obat</th>
                    <th class="text-center" style="width: 15%;">Jumlah</th>
                    <th class="text-right" style="width: 20%;">Harga Satuan</th>
                    <th class="text-right" style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $detail->obat->nm_obat ?? 'Obat tidak ditemukan' }}</div>
                        <div class="item-type">{{ $detail->obat->jenis ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        {{ $detail->jumlah }} {{ $detail->obat->satuan ?? 'pcs' }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($detail->harga_jual * $detail->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
            <div class="summary-row discount">
                <span class="summary-label">Diskon ({{ $penjualan->diskon }}%)</span>
                <span class="summary-value">- Rp {{ number_format($penjualan->total * $penjualan->diskon / 100, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span class="summary-label">GRAND TOTAL</span>
                <span class="summary-value">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima Kasih Atas Kunjungan Anda!</div>
            <div class="notes">
                Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.<br>
                Simpan struk ini sebagai bukti pembayaran yang sah.<br>
                Untuk informasi lebih lanjut, hubungi customer service kami.
            </div>
            <div class="signature">
                <div class="signature-line">
                    ({{ $penjualan->user->name ?? 'Kasir' }})
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
