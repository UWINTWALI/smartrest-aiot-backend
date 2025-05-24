<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="DoctorProfile",
 *     title="Doctor Profile",
 *     description="Doctor profile information",
 *     required={"doctor_id", "license_no"},
 *     @OA\Property(
 *         property="doctor_id",
 *         type="string",
 *         format="uuid",
 *         description="Doctor UUID (same as user_id)",
 *         example="223e4567-e89b-12d3-a456-426614174001"
 *     ),
 *     @OA\Property(
 *         property="license_no", 
 *         type="string", 
 *         description="Medical license number",
 *         example="MED123456"
 *     ),
 *     @OA\Property(
 *         property="specialty", 
 *         type="string",
 *         description="Medical specialty",
 *         example="Sleep Medicine"
 *     ),
 *     @OA\Property(
 *         property="institution", 
 *         type="string",
 *         description="Healthcare institution",
 *         example="City Medical Center"
 *     ),
 *     @OA\Property(
 *         property="years_experience", 
 *         type="integer",
 *         description="Years of professional experience",
 *         example=10
 *     )
 * )
 */

class DoctorProfile extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'doctor_id';

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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'license_no',
        'specialty',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id', 'user_id');
    }

    /**
     * The patients assigned to this doctor.
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(
            PatientProfile::class,
            'doctor_patients',
            'doctor_id',
            'patient_id'
        )->withPivot('assigned_at');
    }
}
