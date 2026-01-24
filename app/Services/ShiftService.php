<?php

namespace App\Services;

use App\Interfaces\Repositories\ShiftRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ShiftService extends BaseService
{
    public function __construct(ShiftRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllShifts($filters = [])
    {
        return $this->repository->getAllWithRelations($filters);
    }

    public function getAktif()
    {
        return $this->repository->getAktif();
    }

    public function storeShift(array $data)
    {
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            Log::error('Error storing shift: ' . $e->getMessage());
            return false;
        }
    }

    public function updateShift($shiftId, array $data)
    {
        try {
            return $this->update($shiftId, $data);
        } catch (\Exception $e) {
            Log::error('Error updating shift: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteShift($shiftId)
    {
        try {
            return $this->delete($shiftId);
        } catch (\Exception $e) {
            Log::error('Error deleting shift: ' . $e->getMessage());
            return false;
        }
    }

    public function getShiftsByDivisi($divisiId)
    {
        return $this->repository->getByDivisi($divisiId);
    }
}
