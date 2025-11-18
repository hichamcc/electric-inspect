<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'customer_id',
        'equipment_id',
        'inspector_id',
        'inspection_type',
        'inspection_date',
        'inspection_time',
        'result',
        'status',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    /**
     * Get the organization that owns the inspection.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the customer that owns the inspection.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the equipment that is being inspected.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the inspector (user) who performed the inspection.
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Get all of the inspection's files.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(InspectionFile::class, 'fileable');
    }

    /**
     * Get the parameter values for this inspection.
     */
    public function parameterValues(): HasMany
    {
        return $this->hasMany(InspectionParameterValue::class);
    }
}
