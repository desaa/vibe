<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekomendasi Pengadaan TIK - <?= esc($konsolidasi['nomor_rekomendasi']) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    <style>
        /* General styling for preview screen */
        body {
            font-family: 'Tinos', serif;
            background-color: #e2e8f0;
            color: #1a1a1a;
            margin: 0;
            padding: 40px 0;
            font-size: 12pt;
            line-height: 1.5;
        }

        .paper {
            background-color: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 30mm 20mm 20mm 25mm; /* standard left-margin is wider for binding */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat (Header) */
        .kop-surat {
            border-bottom: 4px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
        }
        
        .kop-logo {
            position: absolute;
            left: 0;
            top: 5px;
            width: 70px;
            height: auto;
        }
        
        .kop-header {
            margin-left: 0;
        }

        .kop-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 16pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 18pt;
            font-weight: 800;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kop-header p {
            font-size: 10pt;
            margin: 3px 0 0 0;
            color: #4a5568;
        }

        /* Letter Info & Meta */
        .info-surat {
            width: 100%;
            margin-bottom: 25px;
            font-size: 11pt;
        }

        .info-surat td {
            vertical-align: top;
            padding: 2px 0;
        }

        .info-surat td.label {
            width: 120px;
        }

        .info-surat td.separator {
            width: 15px;
            text-align: center;
        }

        .info-surat td.date-val {
            text-align: right;
        }

        /* Body Text */
        .isi-surat {
            text-align: justify;
            margin-bottom: 25px;
            font-size: 11pt;
            text-indent: 40px;
        }

        /* Items Table */
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }

        .table-items th, .table-items td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
        }

        .table-items th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            text-align: center;
        }

        /* Signature block */
        .signature-block {
            width: 100%;
            margin-top: 40px;
            font-size: 11pt;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
        }

        .signature-table td.space {
            height: 80px;
        }

        /* Print Controls Float */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            gap: 10px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn-print {
            background-color: #10b981;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        .btn-close {
            background-color: #6b7280;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
        }

        .btn-close:hover {
            background-color: #4b5563;
            transform: translateY(-1px);
        }

        /* Printing adjustments */
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                margin: 0;
                padding: 0;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                padding: 15mm 15mm 15mm 20mm;
                width: 100%;
                min-height: auto;
            }

            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Controls -->
    <div class="print-controls">
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229.252a1.125 1.125 0 0 1-1.604 1.58l-1.077-1.077m2.452-1.018a1.125 1.125 0 0 1-1.58 1.602L15 18m0 0-1.5 1.5M15 18v-4.5m0 0H9m6 0a3 3 0 1 1-6 0v4.5m6 0H9M9 18l-1.5 1.5M9 18v-4.5"></path>
            </svg>
            Cetak Surat
        </button>
        <button onclick="window.close()" class="btn-close">
            Tutup
        </button>
    </div>

    <!-- The Paper Document Container -->
    <div class="paper">
        
        <!-- Government Kop Surat (Letterhead) -->
        <div class="kop-surat">
            <!-- Dynamic Kop text matching standard Indonesian Gov layout -->
            <div class="kop-header">
                <h2>PEMERINTAH KABUPATEN / KOTA</h2>
                <h1>DINAS KOMUNIKASI DAN INFORMATIKA</h1>
                <p>Jalan Protokol No. 45 Telp. (021) 123456 Fax. (021) 123457 Kode Pos 12345</p>
                <p style="font-weight: bold; color: #1a1a1a;">Email: diskominfo@pemkab.go.id | Website: www.diskominfo.go.id</p>
            </div>
        </div>

        <!-- Date & Recommendation Metadata Table -->
        <table class="info-surat">
            <tr>
                <td class="label">Nomor</td>
                <td class="separator">:</td>
                <td><?= esc($konsolidasi['nomor_rekomendasi']) ?></td>
                <td class="date-val">Tanggal: <?= date('d M Y', strtotime($konsolidasi['tanggal_rekomendasi'])) ?></td>
            </tr>
            <tr>
                <td class="label">Sifat</td>
                <td class="separator">:</td>
                <td>Penting / Segera</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="separator">:</td>
                <td>1 (Satu) Berkas</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="separator">:</td>
                <td style="font-weight: bold; text-decoration: underline;">Rekomendasi Pengadaan Aset TIK</td>
                <td></td>
            </tr>
        </table>

        <!-- Target Destination -->
        <div style="margin-bottom: 20px; font-size: 11pt;">
            Kepada Yth.<br>
            <strong>Kepala <?= esc($konsolidasi['nama_opd']) ?></strong><br>
            di -<br>
            <span style="text-indent: 20px; display: inline-block;">Tempat</span>
        </div>

        <!-- Body / Content -->
        <div class="isi-surat">
            Berdasarkan Surat Pengantar Usulan Pengadaan Aset TIK dari Dinas/Instansi Saudara dengan Nomor: <strong><?= esc($konsolidasi['nomor_surat_opd']) ?></strong> untuk Tahun Anggaran <strong><?= esc($konsolidasi['tahun_anggaran']) ?></strong>, serta menimbang hasil reviu teknis keselarasan, spesifikasi, dan asas kemanfaatan dari tim evaluasi TIK, maka Dinas Komunikasi dan Informatika dengan ini memberikan <strong>REKOMENDASI</strong> pengadaan atas aset Teknologi Informasi dan Komunikasi berikut:
        </div>

        <!-- Items Table -->
        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Aset</th>
                    <th style="width: 30%;">Spesifikasi Teknis</th>
                    <th style="width: 12%;">Jumlah</th>
                    <th style="width: 18%;">Bidang Pengusul</th>
                    <th style="width: 10%;">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($items as $item): 
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="font-weight: bold;"><?= esc($item['nama_aset']) ?></td>
                        <td style="white-space: pre-line; font-size: 9.5pt;"><?= esc($item['spesifikasi']) ?></td>
                        <td style="text-align: center;"><?= esc($item['jumlah']) ?> <?= esc($item['satuan']) ?></td>
                        <td><?= esc($item['nama_bidang']) ?></td>
                        <td style="text-align: center; font-weight: bold; color: #047857;">DISETUJUI</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Footer / Closing -->
        <div class="isi-surat">
            Demikian rekomendasi ini diberikan untuk dipergunakan sebagaimana mestinya sebagai salah satu syarat pemenuhan administrasi pengadaan aset Teknologi Informasi dan Komunikasi (TIK) di lingkungan Pemerintah Daerah.
        </div>

        <!-- Signature Section -->
        <div class="signature-block">
            <table class="signature-table">
                <tr>
                    <td></td>
                    <td style="text-align: center;">
                        Kepala Dinas Komunikasi dan Informatika<br>
                        Kabupaten / Kota<br>
                        <div style="height: 70px;"></div>
                        <span style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                            <?= esc($konsolidasi['kadin_nama'] ?: 'Kepala Dinas') ?>
                        </span><br>
                        <span>Pembina Utama Muda</span><br>
                        <span>NIP. <?= esc($konsolidasi['kadin_nip'] ?: '------------------') ?></span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <!-- Auto Print Trigger Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Un-comment to trigger print window automatically
            // setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>
