<?php

namespace App\Repositories\Contracts;

interface ClassroomRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function getByAcademicYear(int $academicYearId);
    public function getByGradeLevel(int $gradeLevel);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function assignStudents(int $classroomId, array $studentIds);
    public function getWithRelations();
    public function getStudents(int $classroomId);
}
