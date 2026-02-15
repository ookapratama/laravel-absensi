<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $bulan;
    protected $tahun;
    protected $hariEfektif;
    protected $jenisIzins;
    protected $totalLibur;

    public function __construct($data, $bulan, $tahun, $hariEfektif, $jenisIzins, $totalLibur)
    {
        $this->data = $data;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->hariEfektif = $hariEfektif;
        $this->jenisIzins = $jenisIzins;
        $this->totalLibur = $totalLibur;
    }

    public function view(): View
    {
        // Tetap gunakan template blade yang sudah kita buat karena library ini mendukung rendering dari HTML Blade
        return view('pages.absensi.exports.rekap-excel', [
            'data' => $this->data,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'hariEfektif' => $this->hariEfektif,
            'jenisIzins' => $this->jenisIzins,
            'totalLibur' => $this->totalLibur
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Kita bisa menambahkan styling tambahan di sini jika diperlukan, 
        // tapi library ini akan mencoba membaca style inline dari Blade.
        return [
            // Style untuk header No dan Nama (row 1 & 2)
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
