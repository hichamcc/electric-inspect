<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionParameterValue extends Model
{
    protected $fillable = [
        'inspection_id',
        'equipment_type_parameter_id',
        'value',
    ];

    /**
     * Get the inspection that owns the parameter value.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    /**
     * Get the parameter definition.
     */
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(EquipmentTypeParameter::class, 'equipment_type_parameter_id');
    }
}
