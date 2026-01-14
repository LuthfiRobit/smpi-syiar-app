<?php

namespace App\Repositories\Contracts;

interface TeachingMaterialRepositoryInterface
{
    public function getAllTypes();
    public function getByTeacherAndYear($teacherId, $yearId);
    public function store(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    // For Admin Monitoring
    public function getSummaryByYear($yearId);
}
