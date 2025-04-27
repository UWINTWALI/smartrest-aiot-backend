// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Traits\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->isCustomer($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isAdmin($user) ||
               ($this->isDoctor($user) && $model->role === Role::PATIENT->value) ||
               $this->isSelf($user, $model);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user) || $this->isSelf($user, $model);
    }

    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }
}