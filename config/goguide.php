<?php

/*
 | Aturan bisnis GoGuide (proposal 6.3). Ubah angka di sini, tidak perlu mengutak-atik controller.
 */
return [
    // Batas waktu pembayaran sejak booking dibuat
    'batas_bayar_jam' => 24,

    // Jam mulai tour bawaan (bisa diubah wisatawan di form booking)
    'jam_mulai_default' => '08:00',

    // Toleransi keterlambatan wisatawan (hanya ditampilkan di S&K)
    'toleransi_telat_menit' => 15,

    // Batas jumlah peserta per booking
    'max_peserta' => 20,

    // Kebijakan pengembalian dana, urut dari yang paling awal.
    'refund' => [
        ['min_hari' => 3, 'persen' => 100],
        ['min_hari' => 1, 'persen' => 50],
        ['min_hari' => 0, 'persen' => 0],
    ],

    // Indikator kelayakan guide (proposal 6.3.4): minimal pengalaman (tahun) bila tidak bersertifikat
    'min_pengalaman_tahun' => 1,
];