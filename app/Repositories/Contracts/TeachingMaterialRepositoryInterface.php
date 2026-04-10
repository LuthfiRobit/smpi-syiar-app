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

    // For Admin Monitoring — per-teacher summary
    public function getSummaryByYear($yearId);

    // For Admin — flat paginated module list (new)
    public function getModulesForAdmin($yearId, $teacherId = null, $gradeLevel = null, $subjectId = null, $status = null);

    // For Admin — approve/reject a single module (new)
    public function updateStatus($id, $status, $rejectionNote = null);

    // For Teacher — grouped by grade_level then subject (new)
    public function getByTeacherAndYearGrouped($teacherId, $yearId);
}
