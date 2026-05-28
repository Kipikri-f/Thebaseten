<?php
// =====================================================
// DATE HELPER — returns Indonesian day/month strings
// =====================================================

function getIndonesianDate(): string {
    $days = [
        'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa',
        'Wed' => 'Rabu',   'Thu' => 'Kamis', 'Fri' => "Jum'at", 'Sat' => 'Sabtu',
    ];
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',   4 => 'April',
        5 => 'Mei',     6 => 'Juni',     7 => 'Juli',     8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $hari  = $days[date('D')]  ?? date('D');
    $bulan = $months[(int)date('n')] ?? date('F');
    $tgl   = date('d');
    $thn   = date('Y');

    return "$hari, $tgl $bulan $thn";
}
