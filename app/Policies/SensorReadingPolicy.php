<?php
// app/Policies/SensorReadingPolicy.php

namespace App\Policies;

use App\Models\SensorReading;
use App\Models\User;
use App\Traits\HandlesAuthorization;

class SensorReadingPolicy
{
    use HandlesAuthorization;

    public function view(User $user, SensorReading $reading): bool
    {
        return $this->isAdmin($user) ||
               ($this->isDoctor($user) && $this->isAssignedDoctor($user, $reading->patient)) ||
               ($this->isPatient($user) && $this->isSelf($user, $reading->patient));
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->isPatient($user);
    }

    public function export(User $user, SensorReading $reading): bool
    {
        return $this->view($user, $reading);
    }
}