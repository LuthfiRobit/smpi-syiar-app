<?php

namespace App\Repositories\Eloquents;

use App\Models\TimeSlot;
use App\Repositories\Contracts\TimeSlotRepositoryInterface;

class TimeSlotRepository implements TimeSlotRepositoryInterface
{
    protected $model;

    public function __construct(TimeSlot $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('start_time')->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 10)
    {
        $query = $this->model->newQuery();

        if (!empty($filters['day'])) {
            $query->where('day', $filters['day']);
        }

        if (isset($filters['type']) && $filters['type'] !== '') {
            $isBreak = ($filters['type'] === 'Istirahat');
            $query->where('is_break', $isBreak);
        }

        return $query->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $timeSlot = $this->findById($id);
        $timeSlot->update($data);
        return $timeSlot;
    }

    public function delete(int $id)
    {
        $timeSlot = $this->findById($id);
        return $timeSlot->delete();
    }
}
