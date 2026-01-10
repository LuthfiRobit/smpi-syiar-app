<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function findByNis(string $nis);
    public function getByClassroom(int $classroomId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getWithUser();
    public function getPaginated(int $perPage = 10, array $filters = []);
    public function import(array $data);
    public function resetPassword(int $id);
}
