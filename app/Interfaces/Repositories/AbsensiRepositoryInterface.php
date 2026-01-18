<?php

namespace App\Interfaces\Repositories;

interface AbsensiRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPegawaiTanggal(int $pegawaiId, string $tanggal);
    public function getByPegawaiBulan(int $pegawaiId, int $bulan, int $tahun);
    public function getAbsensiHariIni();
    public function getBelumAbsenHariIni();
}
