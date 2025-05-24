<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="SensorReading",
 *     title="Sensor Reading",
 *     description="Sensor data reading from a smart bed",
 *     required={"reading_id", "patient_id", "bed_id", "sensor_type", "sensor_value", "timestamp"},
 *     @OA\Property(
 *         property="reading_id",
 *         type="string",
 *         format="uuid",
 *         description="Reading UUID",
 *         example="323e4567-e89b-12d3-a456-426614174002"
 *     ),
 *     @OA\Property(
 *         property="patient_id", 
 *         type="string",
 *         format="uuid",
 *         description="Patient UUID",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     ),
 *     @OA\Property(
 *         property="bed_id", 
 *         type="string",
 *         description="Smart bed identifier",
 *         example="BED-12345"
 *     ),
 *     @OA\Property(
 *         property="sensor_type", 
 *         type="string",
 *         enum={"heart_rate", "pressure", "temperature", "humidity", "movement", "sleep_quality"},
 *         description="Type of sensor reading",
 *         example="heart_rate"
 *     ),
 *     @OA\Property(
 *         property="sensor_value", 
 *         type="number",
 *         format="float",
 *         description="Value recorded by the sensor",
 *         example=72.5
 *     ),
 *     @OA\Property(
 *         property="timestamp", 
 *         type="string",
 *         format="date-time",
 *         description="When the reading was taken",
 *         example="2025-05-24T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="notes", 
 *         type="string",
 *         description="Additional information about the reading",
 *         example="Patient was sleeping"
 *     )
 * )
 */

class SensorReading extends Model
{
    use HasFactory, HasUuids;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'reading_id';

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
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'bed_id',
        'sensor_type',
        'sensor_value',
        'sensor_unit',
        'additional_metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'datetime',
        'sensor_value' => 'float',
        'additional_metadata' => 'array',
    ];

    /**
     * Get the patient that owns this sensor reading.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id', 'patient_id');
    }
}
