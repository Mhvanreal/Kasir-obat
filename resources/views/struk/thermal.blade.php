<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $penjualan->nota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
            width: 80mm; /* Width for 80mm thermal printer */
            margin: 0 auto;
            padding: 5mm;
        }

        /* Alternate width for 58mm printer - uncomment if needed */
        /* body { width: 58mm; } */

        .receipt {
            width: 100%;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 1px;
        }

        .header .address {
            font-size: 10px;
            margin: 2px 0;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .separator-double {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 8px 0;
        }

        /* Info */
        .info {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .info-label {
            font-weight: normal;
        }

        .info-value {
            font-weight: bold;
            text-align: right;
        }

        /* Items */
        .items {
            margin: 8px 0;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .item-qty {
            flex: 1;
        }

        .item-price {
            text-align: right;
            flex: 1;
        }

        .item-total {
            text-align: right;
            font-weight: bold;
            flex: 1;
        }

        /* Summary */
        .summary {
            margin-top: 8px;
            font-size: 11px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .summary-label {
            font-weight: normal;
        }

        .summary-value {
            font-weight: bold;
            text-align: right;
        }

        .summary-row.total {
            font-size: 13px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #000;
        }

        .summary-row.total .summary-label,
        .summary-row.total .summary-value {
            font-weight: bold;
        }

        /* Payment */
        .payment {
            margin: 8px 0;
            font-size: 11px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .payment-method {
            text-align: center;
            padding: 5px;
            background: #f0f0f0;
            border: 1px solid #000;
            margin: 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }

        .footer .thank-you {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .footer .info-text {
            margin: 3px 0;
            font-size: 9px;
        }

        .footer .timestamp {
            margin-top: 8px;
            font-size: 9px;
        }

        /* Print styles */
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }

            @page {
                size: 80mm auto; /* Auto height for continuous paper */
                margin: 0;
            }

            /* Hide non-essential elements when printing */
            .no-print {
                display: none;
            }
        }

        /* For 58mm printer - uncomment if needed */
        /*
        @media print {
            body { width: 58mm; }
            @page { size: 58mm auto; }
        }
        */
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>APOTEK CITRA</h1>
            <div class="address">Jl. Contoh No. 123</div>
            <div class="address">Telp: (021) 1234567</div>
        </div>

        <div class="separator-double"></div>

        <!-- Transaction Info -->
        <div class="info">
            <div class="info-row">
                <span class="info-label">No. Nota</span>
                <span class="info-value">{{ $penjualan->nota }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $penjualan->tgl_nota->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $penjualan->user->name ?? '-' }}</span>
            </div>
            @if($penjualan->pelanggan)
            <div class="info-row">
                <span class="info-label">Pelanggan</span>
                <span class="info-value">{{ $penjualan->pelanggan->nm_pelanggan }}</span>
            </div>
            @endif
        </div>

        <div class="separator"></div>

        <!-- Items -->
        <div class="items">
            @foreach($penjualan->details as $detail)
            <div class="item">
                <div class="item-name">{{ $detail->obat->nm_obat ?? 'N/A' }}</div>
                <div class="item-detail">
                    <span class="item-qty">{{ $detail->jumlah }} x {{ number_format($detail->harga_jual, 0, ',', '.') }}</span>
                    <span class="item-total">{{ number_format($detail->harga_jual * $detail->jumlah, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="separator"></div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
            <div class="summary-row">
                <span class="summary-label">Diskon ({{ $penjualan->diskon }}%)</span>
                <span class="summary-value">Rp {{ number_format($penjualan->total * $penjualan->diskon / 100, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span class="summary-label">TOTAL</span>
                <span class="summary-value">Rp {{ number_format($penjualan->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="separator-double"></div>

        <!-- Payment Method -->
        <div class="payment">
            <div class="payment-method">
                {{ strtoupper($penjualan->metode_pembayaran) }}
            </div>
        </div>

        <div class="separator"></div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima Kasih</div>
            <div class="info-text">Barang yang sudah dibeli</div>
            <div class="info-text">tidak dapat ditukar/dikembalikan</div>
            <div class="info-text">Simpan struk sebagai bukti</div>
            <div class="timestamp">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        <div class="separator-double"></div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
