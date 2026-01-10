<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getActiveUsers();
    public function getUsersByRole(string $role);
}
