<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentTypeParameter extends Model
{
    protected $fillable = [
        'equipment_type_id',
        'name',
        'label',
        'is_required',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the equipment type that owns the parameter.
     */
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    /**
     * Get the inspection parameter values for this parameter.
     */
    public function inspectionValues(): HasMany
    {
        return $this->hasMany(InspectionParameterValue::class);
    }
}
