<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
    ];

    /**
     * Get the organization that owns the equipment type.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the parameters for the equipment type.
     */
    public function parameters(): HasMany
    {
        return $this->hasMany(EquipmentTypeParameter::class)->orderBy('order');
    }

    /**
     * Get the equipment of this type.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }
}
