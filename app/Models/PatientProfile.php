<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PatientProfile",
 *     title="Patient Profile",
 *     description="Patient profile information",
 *     required={"patient_id"},
 *     @OA\Property(
 *         property="patient_id",
 *         type="string",
 *         format="uuid",
 *         description="Patient UUID (same as user_id)",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     ),
 *     @OA\Property(
 *         property="national_id", 
 *         type="string", 
 *         description="National ID number",
 *         example="AB123456C"
 *     ),
 *     @OA\Property(
 *         property="date_of_birth", 
 *         type="string", 
 *         format="date",
 *         description="Date of birth",
 *         example="1990-01-01"
 *     ),
 *     @OA\Property(
 *         property="sex", 
 *         type="string",
 *         enum={"M", "F", "O"},
 *         description="Biological sex",
 *         example="M"
 *     ),
 *     @OA\Property(
 *         property="emergency_contact_name", 
 *         type="string",
 *         description="Emergency contact name",
 *         example="Jane Doe"
 *     ),
 *     @OA\Property(
 *         property="emergency_contact_phone", 
 *         type="string",
 *         description="Emergency contact phone number",
 *         example="+1234567890"
 *     ),
 *     @OA\Property(
 *         property="health_conditions", 
 *         type="string",
 *         description="Known health conditions",
 *         example="Hypertension, Sleep Apnea"
 *     ),
 *     @OA\Property(
 *         property="medications", 
 *         type="string",
 *         description="Current medications",
 *         example="Lisinopril, CPAP therapy"
 *     )
 * )
 */

class PatientProfile extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'patient_id';

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
        'patient_id',
        'national_id',
        'date_of_birth',
        'sex',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id', 'user_id');
    }

    /**
     * The doctors that are assigned to this patient.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(
            DoctorProfile::class,
            'doctor_patients',
            'patient_id',
            'doctor_id'
        )->withPivot('assigned_at');
    }
}
