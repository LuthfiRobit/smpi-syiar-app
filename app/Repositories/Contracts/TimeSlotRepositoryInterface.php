<?php

namespace App\Repositories\Contracts;

interface TimeSlotRepositoryInterface
{
    public function getAll();
    public function getPaginated(array $filters = [], int $perPage = 10);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
