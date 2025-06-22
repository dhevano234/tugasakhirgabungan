<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian - {{ $antrian->no_antrian }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .ticket-container {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            border: 2px solid #007bff;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }

        .ticket-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-align: center;
            padding: 15px 10px;
            position: relative;
        }

        .clinic-logo {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .clinic-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .clinic-subtitle {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .clinic-address {
            font-size: 9px;
            opacity: 0.9;
        }

        .ticket-body {
            padding: 15px;
            background: white;
        }

        .queue-number-section {
            text-align: center;
            margin-bottom: 15px;
            padding: 15px;
            border: 2px dashed #007bff;
            border-radius: 8px;
            background: rgba(0, 123, 255, 0.05);
        }

        .queue-number {
            font-size: 32px;
            font-weight: 900;
            color: #007bff;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .queue-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            border-bottom: 1px dotted #ddd;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            padding: 6px 0;
            width: 40%;
            font-size: 10px;
            vertical-align: top;
        }

        .info-value {
            font-weight: 500;
            color: #333;
            text-align: right;
            padding: 6px 0;
            font-size: 10px;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-menunggu { 
            background: #fff3cd; 
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-dipanggil { 
            background: #d1ecf1; 
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-selesai { 
            background: #d4edda; 
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-dibatalkan { 
            background: #f8d7da; 
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .ticket-footer {
            background: #f8f9fa;
            padding: 12px 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 2px dashed #ddd;
        }

        .footer-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .footer-note {
            margin: 2px 0;
            line-height: 1.3;
        }

        .print-time {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-style: italic;
            color: #888;
        }

        /* Decorative elements */
        .decoration {
            text-align: center;
            margin: 10px 0;
            color: #007bff;
        }

        .divider {
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, #007bff, transparent);
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        {{-- Header Tiket --}}
        <div class="ticket-header">
            <div class="clinic-logo">🏥</div>
            <div class="clinic-name">KLINIK PRATAMA</div>
            <div class="clinic-subtitle">HADIANA SEHAT</div>
            <div class="clinic-address">Jl. Raya Banjaran No. 658A, Pameungpeuk - Kab. Bandung</div>
        </div>

        {{-- Body Tiket --}}
        <div class="ticket-body">
            {{-- Nomor Antrian --}}
            <div class="queue-number-section">
                <div class="queue-number">{{ $antrian->no_antrian }}</div>
                <div class="queue-label">Nomor Antrian</div>
            </div>

            <hr class="divider">

            {{-- Informasi Detail --}}
            <table class="info-table">
                <tr class="info-row">
                    <td class="info-label">Nama Pasien</td>
                    <td class="info-value">{{ $antrian->name }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">No. HP</td>
                    <td class="info-value">{{ $antrian->phone }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Jenis Kelamin</td>
                    <td class="info-value">{{ $antrian->gender }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Poli</td>
                    <td class="info-value"><strong>{{ $antrian->poli }}</strong></td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Dokter</td>
                    <td class="info-value">{{ $antrian->doctor->nama ?? 'Belum ditentukan' }}</td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Tanggal</td>
                    <td class="info-value"><strong>{{ $antrian->formatted_tanggal }}</strong></td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Urutan ke</td>
                    <td class="info-value"><strong>#{{ $antrian->urutan }}</strong></td>
                </tr>
                <tr class="info-row">
                    <td class="info-label">Status</td>
                    <td class="info-value">
                        <span class="status-badge status-{{ $antrian->status }}">
                            {{ ucfirst($antrian->status) }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="decoration">★ ★ ★</div>
        </div>

        {{-- Footer Tiket --}}
        <div class="ticket-footer">
            <div class="footer-title">HARAP SIMPAN TIKET INI</div>
            <div class="footer-note">Tunjukkan tiket saat nama Anda dipanggil</div>
            <div class="footer-note">Datang 15 menit sebelum jadwal praktek</div>
            <div class="footer-note">Hubungi kami: 0821-1234-5678</div>
            
            <div class="print-time">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>

    {{-- Barcode atau QR Code section (opsional) --}}
    <div style="text-align: center; margin-top: 10px; font-size: 8px; color: #999;">
        ID: {{ $antrian->id }} | {{ $antrian->no_antrian }}
    </div>
</body>
</html>