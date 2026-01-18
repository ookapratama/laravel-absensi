<?php

namespace App\Repositories;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Interfaces\Repositories\AbsensiRepositoryInterface;

class AbsensiRepository extends BaseRepository implements AbsensiRepositoryInterface
{
    public function __construct(Absensi $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('pegawai')->orderBy('tanggal', 'desc')->get();
    }

    public function getByPegawaiTanggal($pegawaiId, string $tanggal)
    {
        return $this->model->where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $tanggal)
            ->first();
    }

    public function getByPegawaiBulan($pegawaiId, $bulan, $tahun)
    {
        return $this->model->where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function getAbsensiHariIni()
    {
        return $this->model->with('pegawai')
            ->whereDate('tanggal', today())
            ->get();
    }

    public function getBelumAbsenHariIni()
    {
        $sudahAbsen = $this->model->whereDate('tanggal', today())
            ->pluck('pegawai_id');

        return Pegawai::aktif()
            ->whereNotIn('id', $sudahAbsen)
            ->with(['divisi', 'kantor'])
            ->get();
    }
}
