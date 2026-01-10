<?php

namespace App\Repositories\Eloquents;

use App\Models\Student;
use App\Models\User;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentRepository implements StudentRepositoryInterface
{
    protected $model;

    public function __construct(Student $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['user', 'classroom'])->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['user', 'classroom'])->findOrFail($id);
    }

    public function findByNis(string $nis)
    {
        return $this->model->where('nis', $nis)->first();
    }

    public function getByClassroom(int $classroomId)
    {
        return $this->model->where('classroom_id', $classroomId)->with('user')->get();
    }

    public function getWithUser()
    {
        return $this->model->with(['user', 'classroom'])->get();
    }

    public function getPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->model->with(['user', 'classroom']);

        if (isset($filters['classroom_id']) && $filters['classroom_id'] !== '') {
            $query->where('classroom_id', $filters['classroom_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $status = $filters['status'] === 'active';
            $query->whereHas('user', function ($q) use ($status) {
                $q->where('is_active', $status);
            });
        }

        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create User account first
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['nis'],
                'email' => $data['email'],
                'password' => Hash::make($data['nis']), // Password default: NIS
                'role' => 'student',
                'is_active' => true,
            ]);

            // Create Student with user_id
            $studentData = [
                'user_id' => $user->id,
                'classroom_id' => $data['classroom_id'] ?? null,
                'nis' => $data['nis'],
                'nisn' => $data['nisn'] ?? null,
                'name' => $data['name'],
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];

            return $this->model->create($studentData);
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $student = $this->findById($id);

            // Update Student data
            $student->update([
                'classroom_id' => $data['classroom_id'] ?? $student->classroom_id,
                'nis' => $data['nis'],
                'nisn' => $data['nisn'] ?? $student->nisn,
                'name' => $data['name'],
                'gender' => $data['gender'] ?? $student->gender,
                'phone' => $data['phone'] ?? $student->phone,
                'address' => $data['address'] ?? $student->address,
            ]);

            // Update User data if email changed
            if (isset($data['email']) && $student->user) {
                $student->user->update([
                    'name' => $data['name'],
                    'username' => $data['nis'],
                    'email' => $data['email'],
                ]);
            }

            return $student->fresh();
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $student = $this->findById($id);
            $user = $student->user;

            // Delete student first
            $student->delete();

            // Then delete associated user
            if ($user) {
                $user->delete();
            }

            return true;
        });
    }

    public function import(array $data)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($data as $index => $row) {
            try {
                // Check if NIS or Email already exists
                if ($this->findByNis($row['nis'])) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": NIS {$row['nis']} sudah terdaftar";
                    continue;
                }

                if (User::where('email', $row['email'])->exists()) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": Email {$row['email']} sudah terdaftar";
                    continue;
                }

                if (User::where('username', $row['nis'])->exists()) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": Username (NIS) {$row['nis']} sudah terdaftar sebagai user";
                    continue;
                }

                // Create student with user
                $this->create($row);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return $results;
    }

    public function resetPassword(int $id)
    {
        return DB::transaction(function () use ($id) {
            $student = $this->findById($id);
            $user = $student->user;

            if ($user) {
                $user->update([
                    'username' => $student->nis,
                    'password' => Hash::make($student->nis)
                ]);
                return true;
            }

            return false;
        });
    }
}
