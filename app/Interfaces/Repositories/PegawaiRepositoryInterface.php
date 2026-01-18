<?php

namespace App\Interfaces\Repositories;

interface PegawaiRepositoryInterface extends BaseRepositoryInterface
{
    public function getAktif();
    public function getByUserId(int $userId);
    public function getWithRelations(int $id);
}
