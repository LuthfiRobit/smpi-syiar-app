<?php

namespace App\Repositories\Contracts;

interface SchoolIdentityRepositoryInterface
{
    public function get();
    public function update(array $data);
}
