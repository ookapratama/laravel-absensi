<?php

namespace App\Repositories;

use App\Models\Pegawai;
use App\Interfaces\Repositories\PegawaiRepositoryInterface;

class PegawaiRepository extends BaseRepository implements PegawaiRepositoryInterface
{
    public function __construct(Pegawai $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with(['user', 'divisi', 'kantor'])->get();
    }

    public function getAktif()
    {
        return $this->model->aktif()->with(['user', 'divisi', 'kantor'])->get();
    }

    public function getByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function getWithRelations($id)
    {
        return $this->model->with(['user', 'divisi', 'kantor', 'lokasiAbsen'])->findOrFail($id);
    }

    public function paginate($perPage = 10)
    {
        return $this->model->with(['user', 'divisi', 'kantor'])->paginate($perPage);
    }

    public function rekapPaginate($bulan, $tahun, $perPage = 10)
    {
        return $this->model->aktif()
            ->with(['divisi', 'absensis' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            }])
            ->paginate($perPage);
    }
}
