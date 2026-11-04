<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Basis Perhitungan Laba Bersih Agen JNE
    |--------------------------------------------------------------------------
    | Pilihan:
    | - 'discount_charge' : Menggunakan SUM(discount_charge) sebagai pendapatan komisi agen (Default dokumen JNE)
    | - 'grand_total'     : Menggunakan SUM(grand_total) (Perilaku lama)
    */
    'laba_basis' => env('LABA_BASIS', 'discount_charge'),

    /*
    |--------------------------------------------------------------------------
    | Sertakan Komisi Bulanan dalam Perhitungan Laba Bersih
    |--------------------------------------------------------------------------
    | Jika true, SUM(nominal) transfer komisi bulanan akan ditambahkan ke Laba Bersih.
    */
    'laba_include_komisi_bulanan' => env('LABA_INCLUDE_KOMISI', false),

    /*
    |--------------------------------------------------------------------------
    | Lag Bulan Transfer Komisi Bulanan untuk Rekonsiliasi Sistem
    |--------------------------------------------------------------------------
    | 0 = transfer bulan M direkonsiliasi dengan Packing List bulan M yang sama.
    | 1 = transfer bulan M direkonsiliasi dengan Packing List bulan M-1 (bulan lalu).
    */
    'komisi_transfer_lag_bulan' => (int) env('KOMISI_TRANSFER_LAG_BULAN', 0),
];
