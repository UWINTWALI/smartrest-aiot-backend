// app/Traits/HandlesAuthorization.php

namespace App\Traits;

use App\Enums\Role;
use App\Models\User;

trait HandlesAuthorization
{
protected function isAdmin(User $user): bool
{
return $user->role === Role::ADMIN->value;
}

protected function isDoctor(User $user): bool
{
return $user->role === Role::DOCTOR->value;
}

protected function isPatient(User $user): bool
{
return $user->role === Role::PATIENT->value;
}

protected function isCustomer(User $user): bool
{
return $user->role === Role::CUSTOMER->value;
}

protected function isSelf(User $user, mixed $model): bool
{
return $user->user_id === $model->user_id;
}

protected function isAssignedDoctor(User $user, mixed $model): bool
{
if (!$this->isDoctor($user)) {
return false;
}

// Check if the doctor is assigned to this patient
return $model->doctors()->where('doctor_id', $user->user_id)->exists();
}
}