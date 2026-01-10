<?php

namespace App\Repositories\Contracts;

interface SubjectRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function findByCode(string $code);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
