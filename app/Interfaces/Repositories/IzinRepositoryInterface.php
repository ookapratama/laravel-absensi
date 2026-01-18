<?php

namespace App\Interfaces\Repositories;

interface IzinRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPegawai(int $pegawaiId);
    public function getPending();
    public function getApproved();
    public function checkOverlap(int $pegawaiId, string $tglMulai, string $tglSelesai, ?int $excludeId = null);
}
