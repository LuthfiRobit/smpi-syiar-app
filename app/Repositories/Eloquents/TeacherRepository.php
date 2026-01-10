<?php

namespace App\Repositories\Eloquents;

use App\Models\Teacher;
use App\Models\User;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherRepository implements TeacherRepositoryInterface
{
    protected $model;

    public function __construct(Teacher $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('user')->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('user')->findOrFail($id);
    }

    public function findByNip(string $nip)
    {
        return $this->model->where('nip', $nip)->first();
    }

    public function getWithUser()
    {
        return $this->model->with('user')->get();
    }

    public function getPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->model->with('user');

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
                'username' => $data['nip'],
                'email' => $data['email'],
                'password' => Hash::make($data['nip']), // Password default: NIP
                'role' => 'teacher',
                'is_active' => true,
            ]);

            // Create Teacher with user_id
            $teacherData = [
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'name' => $data['name'],
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];

            return $this->model->create($teacherData);
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $teacher = $this->findById($id);

            // Update Teacher data
            $teacher->update([
                'nip' => $data['nip'],
                'name' => $data['name'],
                'gender' => $data['gender'] ?? $teacher->gender,
                'phone' => $data['phone'] ?? $teacher->phone,
                'address' => $data['address'] ?? $teacher->address,
            ]);

            // Update User data if email changed
            if (isset($data['email']) && $teacher->user) {
                $teacher->user->update([
                    'name' => $data['name'],
                    'username' => $data['nip'],
                    'email' => $data['email'],
                ]);
            }

            return $teacher->fresh();
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $teacher = $this->findById($id);
            $user = $teacher->user;

            // Delete teacher first
            $teacher->delete();

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
                // Check if NIP or Email already exists
                if ($this->findByNip($row['nip'])) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": NIP {$row['nip']} sudah terdaftar";
                    continue;
                }

                if (User::where('email', $row['email'])->exists()) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": Email {$row['email']} sudah terdaftar";
                    continue;
                }

                if (User::where('username', $row['nip'])->exists()) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 1) . ": Username (NIP) {$row['nip']} sudah terdaftar sebagai user";
                    continue;
                }

                // Create teacher with user
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
            $teacher = $this->findById($id);
            $user = $teacher->user;

            if ($user) {
                $user->update([
                    'username' => $teacher->nip,
                    'password' => Hash::make($teacher->nip)
                ]);
                return true;
            }

            return false;
        });
    }
}
