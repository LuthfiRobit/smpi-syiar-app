<?php

namespace App\Repositories\Eloquents;

use App\Models\SchoolIdentity;
use App\Repositories\Contracts\SchoolIdentityRepositoryInterface;

class SchoolIdentityRepository implements SchoolIdentityRepositoryInterface
{
    protected $model;

    public function __construct(SchoolIdentity $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        // Ambil record pertama (seharusnya hanya ada 1 record)
        return $this->model->first();
    }

    public function update(array $data)
    {
        $identity = $this->get();

        if ($identity) {
            $identity->update($data);
        } else {
            $identity = $this->model->create($data);
        }

        return $identity;
    }
}
