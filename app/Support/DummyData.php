<?php

namespace App\Support;

class DummyData
{
    // ==========================================
    // DUMMY MASTER STATUS
    // ==========================================
    public static function getStatuses(): array
    {
        return [
            [
                'id_status' => 'STS-001',
                'nama_status' => 'Aktif',
                'level' => '1',
                'ket_status' => 'Status user atau operasional aktif',
            ],
            [
                'id_status' => 'STS-002',
                'nama_status' => 'Non-Aktif',
                'level' => '2',
                'ket_status' => 'Status user atau akun dinonaktifkan sementara',
            ],
            [
                'id_status' => 'STS-003',
                'nama_status' => 'Cuti',
                'level' => '3',
                'ket_status' => 'Status karyawan dalam masa cuti',
            ],
            [
                'id_status' => 'STS-004',
                'nama_status' => 'Suspended',
                'level' => '4',
                'ket_status' => 'Status dibekukan karena pelanggaran SOP',
            ],
        ];
    }

    // ==========================================
    // DUMMY MASTER USER
    // ==========================================
    public static function getUsers(): array
    {
        return [
            [
                'id_user' => 'USR-001',
                'nama_user' => 'owner_utama',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Tomang Raya No. 12, Jakarta Barat',
                'status' => 'Aktif',
                'level' => 'Owner',
                'password' => '********',
            ],
            [
                'id_user' => 'USR-002',
                'nama_user' => 'admin_operasional',
                'no_hp' => '081398765432',
                'alamat' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
                'status' => 'Aktif',
                'level' => 'Admin',
                'password' => '********',
            ],
            [
                'id_user' => 'USR-003',
                'nama_user' => 'admin_packing',
                'no_hp' => '081512344321',
                'alamat' => 'Jl. Daan Mogot Km 11, Jakarta Barat',
                'status' => 'Aktif',
                'level' => 'Admin',
                'password' => '********',
            ],
            [
                'id_user' => 'USR-004',
                'nama_user' => 'staf_gudang',
                'no_hp' => '081776543210',
                'alamat' => 'Jl. Meruya Ilir No. 88, Jakarta Barat',
                'status' => 'Non-Aktif',
                'level' => 'Admin',
                'password' => '********',
            ],
        ];
    }

    // ==========================================
    // DUMMY MASTER CABANG
    // ==========================================
    public static function getCabangs(): array
    {
        return [
            [
                'id_cab' => 'CAB-001',
                'nama_cabang' => 'Cabang Tomang Raya',
                'alamat' => 'Jl. Tomang Raya No. 11, Jakarta Barat',
                'nama_pic' => 'Budi Santoso',
                'no_hp' => '081122334455',
            ],
            [
                'id_cab' => 'CAB-002',
                'nama_cabang' => 'Cabang Kebon Jeruk',
                'alamat' => 'Jl. Surya Mandala No. 8, Jakarta Barat',
                'nama_pic' => 'Siti Nurhaliza',
                'no_hp' => '081233445566',
            ],
            [
                'id_cab' => 'CAB-003',
                'nama_cabang' => 'Cabang Cengkareng Hub',
                'alamat' => 'Jl. Daan Mogot Km 14, Jakarta Barat',
                'nama_pic' => 'Hendra Setiawan',
                'no_hp' => '081344556677',
            ],
            [
                'id_cab' => 'CAB-004',
                'nama_cabang' => 'Cabang Meruya Selatan',
                'alamat' => 'Jl. Meruya Selatan No. 25, Jakarta Barat',
                'nama_pic' => 'Rina Wijayanti',
                'no_hp' => '081455667788',
            ],
        ];
    }

    // ==========================================
    // DUMMY MASTER OPS
    // ==========================================
    public static function getOps(): array
    {
        return [
            [
                'id_ops' => 'OPS-001',
                'beban_ops' => 'Listrik & Air Kantor',
                'ket_ops' => 'Tagihan utilitas PLN & PAM bulanan kantor operasional',
            ],
            [
                'id_ops' => 'OPS-002',
                'beban_ops' => 'Bahan Bakar & Tol Armada',
                'ket_ops' => 'Bensin pertalite/solar dan uang tol kurir pick up harian',
            ],
            [
                'id_ops' => 'OPS-003',
                'beban_ops' => 'Lakban & Perlengkapan Packing',
                'ket_ops' => 'Pembelian lakban cokelat, bubble wrap, plastik wrapping, kardus',
            ],
            [
                'id_ops' => 'OPS-004',
                'beban_ops' => 'Konsumsi & Lembur Karyawan',
                'ket_ops' => 'Biaya makan malam staf sortasi saat overload malam hari',
            ],
            [
                'id_ops' => 'OPS-005',
                'beban_ops' => 'Perawatan Kendaraan & Servis',
                'ket_ops' => 'Ganti oli, servis berkala motor pickup dan mobil van',
            ],
        ];
    }

    // ==========================================
    // DUMMY TRANSAKSI PACKING LIST
    // ==========================================
    public static function getPackingLists(): array
    {
        return [
            [
                'kode_trx_pl' => 'TPL-20250105-001',
                'tanggal_pl' => '05-01-2025',
                'total' => 2450000.00,
                'discount_charge' => 125000.00,
                'grand_total' => 2325000.00,
                'jumlah_paket' => 142,
                'ket_trx_pl' => 'Pengiriman barang reguler & YES sesi pagi-sore',
            ],
            [
                'kode_trx_pl' => 'TPL-20250112-002',
                'tanggal_pl' => '12-01-2025',
                'total' => 3100000.00,
                'discount_charge' => 150000.00,
                'grand_total' => 2950000.00,
                'jumlah_paket' => 185,
                'ket_trx_pl' => 'Pengiriman e-commerce weekend promo',
            ],
            [
                'kode_trx_pl' => 'TPL-20250120-003',
                'tanggal_pl' => '20-01-2025',
                'total' => 1850000.00,
                'discount_charge' => 95000.00,
                'grand_total' => 1755000.00,
                'jumlah_paket' => 110,
                'ket_trx_pl' => 'Pick up marketplace Tokopedia & Shopee',
            ],
            [
                'kode_trx_pl' => 'TPL-20250203-004',
                'tanggal_pl' => '03-02-2025',
                'total' => 2800000.00,
                'discount_charge' => 140000.00,
                'grand_total' => 2660000.00,
                'jumlah_paket' => 165,
                'ket_trx_pl' => 'Awal bulan lonjakan pengiriman gajian',
            ],
            [
                'kode_trx_pl' => 'TPL-20250215-005',
                'tanggal_pl' => '15-02-2025',
                'total' => 3450000.00,
                'discount_charge' => 175000.00,
                'grand_total' => 3275000.00,
                'jumlah_paket' => 210,
                'ket_trx_pl' => 'Event Valentine & Chinese New Year cargo',
            ],
            [
                'kode_trx_pl' => 'TPL-20250301-006',
                'tanggal_pl' => '01-03-2025',
                'total' => 2100000.00,
                'discount_charge' => 100000.00,
                'grand_total' => 2000000.00,
                'jumlah_paket' => 128,
                'ket_trx_pl' => 'Pengiriman normal awal bulan Maret',
            ],
        ];
    }

    // ==========================================
    // DUMMY TRANSAKSI OPERASIONAL
    // ==========================================
    public static function getOperasionals(): array
    {
        return [
            [
                'kode_trx_ops' => 'TOP-20250106-001',
                'tanggal_trx_ops' => '06-01-2025',
                'id_ops' => 'OPS-001',
                'beban_ops' => 'Listrik & Air Kantor',
                'nominal' => 450000.00,
                'ket_trx_ops' => 'Bayar token PLN kantor cabang Tomang',
            ],
            [
                'kode_trx_ops' => 'TOP-20250114-002',
                'tanggal_trx_ops' => '14-01-2025',
                'id_ops' => 'OPS-002',
                'beban_ops' => 'Bahan Bakar & Tol Armada',
                'nominal' => 320000.00,
                'ket_trx_ops' => 'Bensin motor kurir minggu ke-2',
            ],
            [
                'kode_trx_ops' => 'TOP-20250125-003',
                'tanggal_trx_ops' => '25-01-2025',
                'id_ops' => 'OPS-003',
                'beban_ops' => 'Lakban & Perlengkapan Packing',
                'nominal' => 275000.00,
                'ket_trx_ops' => 'Beli 2 dus lakban cokelat + 1 roll bubble wrap',
            ],
            [
                'kode_trx_ops' => 'TOP-20250208-004',
                'tanggal_trx_ops' => '08-02-2025',
                'id_ops' => 'OPS-005',
                'beban_ops' => 'Perawatan Kendaraan & Servis',
                'nominal' => 350000.00,
                'ket_trx_ops' => 'Ganti oli dan kampas rem motor operasional B 3456 TMG',
            ],
            [
                'kode_trx_ops' => 'TOP-20250220-005',
                'tanggal_trx_ops' => '20-02-2025',
                'id_ops' => 'OPS-004',
                'beban_ops' => 'Konsumsi & Lembur Karyawan',
                'nominal' => 180000.00,
                'ket_trx_ops' => 'Makan malam lembur tim sortir banjir kiriman',
            ],
            [
                'kode_trx_ops' => 'TOP-20250302-006',
                'tanggal_trx_ops' => '02-03-2025',
                'id_ops' => 'OPS-001',
                'beban_ops' => 'Listrik & Air Kantor',
                'nominal' => 470000.00,
                'ket_trx_ops' => 'Tagihan PAM & Listrik awal Maret',
            ],
        ];
    }

    // ==========================================
    // DUMMY TRANSAKSI KOMISI BULANAN
    // ==========================================
    public static function getKomisis(): array
    {
        return [
            [
                'kode_trx_cl' => 'TCL-20250131-001',
                'tanggal_tf' => '31-01-2025',
                'nominal' => 5250000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Januari 2025',
            ],
            [
                'kode_trx_cl' => 'TCL-20250228-002',
                'tanggal_tf' => '28-02-2025',
                'nominal' => 6100000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Februari 2025',
            ],
            [
                'kode_trx_cl' => 'TCL-20250331-003',
                'tanggal_tf' => '31-03-2025',
                'nominal' => 4900000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Maret 2025',
            ],
        ];
    }

    // ==========================================
    // DASHBOARD SUMMARY
    // ==========================================
    public static function getDashboardSummary(): array
    {
        $packingLists = self::getPackingLists();
        $operasionals = self::getOperasionals();
        $komisis = self::getKomisis();

        $totalPL = array_sum(array_column($packingLists, 'grand_total'));
        $totalOps = array_sum(array_column($operasionals, 'nominal'));
        $totalKomisi = array_sum(array_column($komisis, 'nominal'));
        $labaBersih = $totalPL - $totalOps;

        return [
            'total_packing_list' => $totalPL,
            'total_operasional' => $totalOps,
            'total_komisi' => $totalKomisi,
            'laba_bersih_bulan_ini' => $labaBersih,
            'total_paket' => array_sum(array_column($packingLists, 'jumlah_paket')),
            'total_trx_pl' => count($packingLists),
            'total_trx_ops' => count($operasionals),
        ];
    }

    // ==========================================
    // REKAP LAPORAN LABA BERSIH DUMMY
    // ==========================================
    public static function getLabaBersihRekap(): array
    {
        return [
            [
                'bulan_tahun' => 'Januari 2025',
                'total_pl' => 7030000.00,
                'total_ops' => 1045000.00,
                'laba_bersih' => 5985000.00,
                'status' => 'Surplus',
            ],
            [
                'bulan_tahun' => 'Februari 2025',
                'total_pl' => 5935000.00,
                'total_ops' => 530000.00,
                'laba_bersih' => 5405000.00,
                'status' => 'Surplus',
            ],
            [
                'bulan_tahun' => 'Maret 2025',
                'total_pl' => 2000000.00,
                'total_ops' => 470000.00,
                'laba_bersih' => 1530000.00,
                'status' => 'Surplus',
            ],
        ];
    }

    // ==========================================
    // REKAP LAPORAN PACKING LIST PER BULAN
    // ==========================================
    public static function getPackingListRekap(): array
    {
        return [
            [
                'bulan_tahun' => 'Januari 2025',
                'total_discount_charge' => 370000.00,
                'total_grand_total' => 7030000.00,
                'total_paket' => 437,
                'jumlah_trx' => 3,
            ],
            [
                'bulan_tahun' => 'Februari 2025',
                'total_discount_charge' => 315000.00,
                'total_grand_total' => 5935000.00,
                'total_paket' => 375,
                'jumlah_trx' => 2,
            ],
            [
                'bulan_tahun' => 'Maret 2025',
                'total_discount_charge' => 100000.00,
                'total_grand_total' => 2000000.00,
                'total_paket' => 128,
                'jumlah_trx' => 1,
            ],
        ];
    }

    // ==========================================
    // REKAP LAPORAN OPERASIONAL PER BULAN
    // ==========================================
    public static function getOperasionalRekap(): array
    {
        return [
            [
                'bulan_tahun' => 'Januari 2025',
                'total_nominal' => 1045000.00,
                'jumlah_trx' => 3,
            ],
            [
                'bulan_tahun' => 'Februari 2025',
                'total_nominal' => 530000.00,
                'jumlah_trx' => 2,
            ],
            [
                'bulan_tahun' => 'Maret 2025',
                'total_nominal' => 470000.00,
                'jumlah_trx' => 1,
            ],
        ];
    }

    // ==========================================
    // REKAP LAPORAN KOMISI PER BULAN
    // ==========================================
    public static function getKomisiRekap(): array
    {
        return [
            [
                'bulan_tahun' => 'Januari 2025',
                'total_nominal' => 5250000.00,
                'jumlah_tf' => 1,
            ],
            [
                'bulan_tahun' => 'Februari 2025',
                'total_nominal' => 6100000.00,
                'jumlah_tf' => 1,
            ],
            [
                'bulan_tahun' => 'Maret 2025',
                'total_nominal' => 4900000.00,
                'jumlah_tf' => 1,
            ],
        ];
    }
}
