<?php

namespace App\Repositories\Contracts;

interface AcademicYearRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function getActive();
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function setActive(int $id);
}
