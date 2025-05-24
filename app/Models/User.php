<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     required={"user_id", "first_name", "last_name", "email", "role"},
 *     @OA\Property(
 *         property="user_id",
 *         type="string",
 *         format="uuid",
 *         description="User UUID",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     ),
 *     @OA\Property(
 *         property="first_name", 
 *         type="string", 
 *         description="User first name",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="last_name", 
 *         type="string", 
 *         description="User last name",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="email", 
 *         type="string", 
 *         format="email",
 *         description="User email address",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="phone", 
 *         type="string", 
 *         description="User phone number",
 *         example="+1234567890"
 *     ),
 *     @OA\Property(
 *         property="role", 
 *         type="string", 
 *         enum={"patient", "doctor", "customer", "admin"},
 *         description="User role",
 *         example="patient"
 *     ),
 *     @OA\Property(
 *         property="is_email_verified", 
 *         type="boolean", 
 *         description="Email verification status",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="email_verified_at", 
 *         type="string", 
 *         format="date-time",
 *         description="Email verification timestamp",
 *         example="2025-05-24T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at", 
 *         type="string", 
 *         format="date-time",
 *         description="Creation timestamp",
 *         example="2025-05-24T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at", 
 *         type="string", 
 *         format="date-time",
 *         description="Last update timestamp",
 *         example="2025-05-24T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="patientProfile", 
 *         ref="#/components/schemas/PatientProfile",
 *         description="Patient profile data (if role is patient)"
 *     ),
 *     @OA\Property(
 *         property="doctorProfile", 
 *         ref="#/components/schemas/DoctorProfile",
 *         description="Doctor profile data (if role is doctor)"
 *     )
 * )
 */

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuids;
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_email_verified' => 'boolean',
        ];
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['user_id'];
    }

    /**
     * Get the patient profile associated with the user.
     */
    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class, 'patient_id', 'user_id');
    }

    /**
     * Get the doctor profile associated with the user.
     */
    public function doctorProfile(): HasOne
    {
        return $this->hasOne(DoctorProfile::class, 'doctor_id', 'user_id');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a doctor.
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if the user is a patient.
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
