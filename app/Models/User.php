<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasPermissions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissions;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'integer';


    // Make sure User model is fillable
    protected $fillable = [
        'user_id',
        'email',
        'password_hash',
        'role',
        'first_name',
        'last_name',
        'phone',
        'is_email_verified',
        'permissions'
    ];
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_email_verified' => 'boolean',
        'permissions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function hasVerifiedEmail()
    {
        return $this->is_email_verified;
    }

    public function markEmailAsVerified()
    {
        $this->is_email_verified = true;
        $this->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    public function addPermission($permission)
    {
        // Only admins can add permissions
        if (Auth::user()->role !== 'admin') {
            return false;
        }

        // Get role-based permissions from config
        $rolePermissions = Config::get('permissions.roles.' . $this->role, []);

        // Don't allow adding permissions that are already in the role's permissions
        if (in_array($permission, $rolePermissions)) {
            return false;
        }

        $permissions = $this->getCustomPermissions();
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->attributes['permissions'] = json_encode($permissions);
            $this->save();
            return true;
        }
        return false;
    }

    public function removePermission($permission)
    {
        // Only admins can remove permissions
        if (Auth::user()->role !== 'admin') {
            return false;
        }

        // Get role-based permissions from config
        $rolePermissions = Config::get('permissions.roles.' . $this->role, []);

        // Don't allow removing permissions that are in the role's permissions
        if (in_array($permission, $rolePermissions)) {
            return false;
        }

        $permissions = $this->getCustomPermissions();
        $this->attributes['permissions'] = array_values(array_diff($permissions, [$permission]));
        $this->save();
        return true;
    }

    protected function getCustomPermissions()
    {
        if (!isset($this->attributes['permissions'])) {
            return [];
        }

        $permissions = json_decode($this->attributes['permissions'], true);
        return is_array($permissions) ? $permissions : [];
    }

    public function getPermissionsAttribute()
    {
        // Get role-based permissions from config
        $rolePermissions = Config::get('permissions.roles.' . $this->role, []);

        // Get custom permissions from database
        $customPermissions = $this->getCustomPermissions();

        // Merge and return unique permissions
        return array_unique(array_merge($rolePermissions, $customPermissions));
    }
}