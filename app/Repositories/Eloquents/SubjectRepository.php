<?php

namespace App\Repositories\Eloquents;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;

class SubjectRepository implements SubjectRepositoryInterface
{
    protected $model;

    public function __construct(Subject $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findByCode(string $code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $subject = $this->findById($id);
        $subject->update($data);
        return $subject;
    }

    public function delete(int $id)
    {
        $subject = $this->findById($id);
        return $subject->delete();
    }
}
