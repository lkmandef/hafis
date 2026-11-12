<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Baseline Awal Operasional Resmi HAFIS
    |--------------------------------------------------------------------------
    |
    | Menetapkan bulan dan tahun awal operasional resmi HAFIS di RSUD dr. H.
    | Moch. Ansari Saleh Banjarmasin. Sistem tidak akan menuntut tunggakan
    | inspeksi dari periode kalender sebelum baseline ini.
    |
    */
    'operational_start_year' => (int) env('HAFIS_START_YEAR', 2026),
    'operational_start_month' => (int) env('HAFIS_START_MONTH', 11), // November 2026
];
