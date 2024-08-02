<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DowntimecodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Human Resources
            ['id' => 1, 'kode' => 'HR01', 'keterangan' => 'Karyawan tidak hadir atau tidak ada yang menggantikan', 'id_section' => 1],
            ['id' => 2, 'kode' => 'HR02', 'keterangan' => 'Training Karyawan', 'id_section' => 1],
            ['id' => 3, 'kode' => 'HR03', 'keterangan' => 'Masalah Kesehatan Karyawan', 'id_section' => 1],
            ['id' => 4, 'kode' => 'HR04', 'keterangan' => 'Rotasi Karyawan', 'id_section' => 1],
            ['id' => 5, 'kode' => 'HR05', 'keterangan' => 'Masalah Komunikasi Internal', 'id_section' => 1],

            // Finance and Accounting
            ['id' => 6, 'kode' => 'FA01', 'keterangan' => 'Kesalahan Pembayaran', 'id_section' => 2],
            ['id' => 7, 'kode' => 'FA02', 'keterangan' => 'Masalah Anggaran', 'id_section' => 2],
            ['id' => 8, 'kode' => 'FA03', 'keterangan' => 'Audit Internal', 'id_section' => 2],
            ['id' => 9, 'kode' => 'FA04', 'keterangan' => 'Pengadaan Bahan Baku', 'id_section' => 2],
            ['id' => 10, 'kode' => 'FA05', 'keterangan' => 'Masalah Pembayaran Vendor', 'id_section' => 2],

            // Marketing
            ['id' => 11, 'kode' => 'MK01', 'keterangan' => 'Perubahan Permintaan', 'id_section' => 3],
            ['id' => 12, 'kode' => 'MK02', 'keterangan' => 'Kampanye Pemasaran', 'id_section' => 3],
            ['id' => 13, 'kode' => 'MK03', 'keterangan' => 'Promosi Penjualan', 'id_section' => 3],
            ['id' => 14, 'kode' => 'MK04', 'keterangan' => 'Masalah Riset Pasar', 'id_section' => 3],
            ['id' => 15, 'kode' => 'MK05', 'keterangan' => 'Feedback Pelanggan', 'id_section' => 3],

            // Sales
            ['id' => 16, 'kode' => 'SL01', 'keterangan' => 'Kesalahan Order', 'id_section' => 4],
            ['id' => 17, 'kode' => 'SL02', 'keterangan' => 'Keterlambatan Pengiriman', 'id_section' => 4],
            ['id' => 18, 'kode' => 'SL03', 'keterangan' => 'Masalah Faktur', 'id_section' => 4],
            ['id' => 19, 'kode' => 'SL04', 'keterangan' => 'Penurunan Penjualan', 'id_section' => 4],
            ['id' => 20, 'kode' => 'SL05', 'keterangan' => 'Masalah Kontrak Penjualan', 'id_section' => 4],

            // Operations
            ['id' => 21, 'kode' => 'OP01', 'keterangan' => 'Kerusakan Mesin', 'id_section' => 5],
            ['id' => 22, 'kode' => 'OP02', 'keterangan' => 'Keterlambatan Pengiriman Bahan Baku', 'id_section' => 5],
            ['id' => 23, 'kode' => 'OP03', 'keterangan' => 'Masalah Kualitas Bahan Baku', 'id_section' => 5],
            ['id' => 24, 'kode' => 'OP04', 'keterangan' => 'Keterlambatan Setup Mesin', 'id_section' => 5],
            ['id' => 25, 'kode' => 'OP05', 'keterangan' => 'Gangguan Operasional', 'id_section' => 5],

            // Production
            ['id' => 26, 'kode' => 'PR01', 'keterangan' => 'Kerusakan Peralatan', 'id_section' => 6],
            ['id' => 27, 'kode' => 'PR02', 'keterangan' => 'Keterlambatan Pengiriman Bahan', 'id_section' => 6],
            ['id' => 28, 'kode' => 'PR03', 'keterangan' => 'Masalah Kualitas Produk', 'id_section' => 6],
            ['id' => 29, 'kode' => 'PR04', 'keterangan' => 'Keterlambatan Setup', 'id_section' => 6],
            ['id' => 30, 'kode' => 'PR05', 'keterangan' => 'Kesalahan Produksi', 'id_section' => 6],

            // Information Technology
            ['id' => 31, 'kode' => 'IT01', 'keterangan' => 'Kerusakan Sistem TI', 'id_section' => 7],
            ['id' => 32, 'kode' => 'IT02', 'keterangan' => 'Masalah Jaringan', 'id_section' => 7],
            ['id' => 33, 'kode' => 'IT03', 'keterangan' => 'Pemeliharaan Sistem', 'id_section' => 7],
            ['id' => 34, 'kode' => 'IT04', 'keterangan' => 'Update Perangkat Lunak', 'id_section' => 7],
            ['id' => 35, 'kode' => 'IT05', 'keterangan' => 'Masalah Keamanan Data', 'id_section' => 7],

            // Customer Service
            ['id' => 36, 'kode' => 'CS01', 'keterangan' => 'Keluhan Pelanggan', 'id_section' => 8],
            ['id' => 37, 'kode' => 'CS02', 'keterangan' => 'Waktu Respon', 'id_section' => 8],
            ['id' => 38, 'kode' => 'CS03', 'keterangan' => 'Masalah Pengembalian Produk', 'id_section' => 8],
            ['id' => 39, 'kode' => 'CS04', 'keterangan' => 'Penanganan RMA', 'id_section' => 8],
            ['id' => 40, 'kode' => 'CS05', 'keterangan' => 'Masalah Pengiriman', 'id_section' => 8],

            // Legal
            ['id' => 41, 'kode' => 'LG01', 'keterangan' => 'Masalah Kepatuhan', 'id_section' => 9],
            ['id' => 42, 'kode' => 'LG02', 'keterangan' => 'Klausul Kontrak', 'id_section' => 9],
            ['id' => 43, 'kode' => 'LG03', 'keterangan' => 'Proses Litigasi', 'id_section' => 9],
            ['id' => 44, 'kode' => 'LG04', 'keterangan' => 'Penegakan Hak Kekayaan Intelektual', 'id_section' => 9],
            ['id' => 45, 'kode' => 'LG05', 'keterangan' => 'Penyelidikan Hukum', 'id_section' => 9],

            // Product Development
            ['id' => 46, 'kode' => 'PD01', 'keterangan' => 'Waktu Pengujian Produk', 'id_section' => 10],
            ['id' => 47, 'kode' => 'PD02', 'keterangan' => 'Modifikasi Desain', 'id_section' => 10],
            ['id' => 48, 'kode' => 'PD03', 'keterangan' => 'Keterlambatan Riset Produk', 'id_section' => 10],
            ['id' => 49, 'kode' => 'PD04', 'keterangan' => 'Masalah Prototipe', 'id_section' => 10],
            ['id' => 50, 'kode' => 'PD05', 'keterangan' => 'Integrasi Teknologi Baru', 'id_section' => 10],
        ];

        DB::table('downtimecode')->insert($data);
    }
}
