<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $role = $this->route('role');
        return [
            'name' => 'sometimes|string|unique:roles,name,' . $role->id,
            'permissions' => 'sometimes|array|exists:permissions,id'
        ];
    }
}