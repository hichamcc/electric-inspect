<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'customer_id',
        'equipment_type_id',
        'equipment_type',
        'manufacturer',
        'model',
        'serial_number',
        'location',
        'description',
        'installation_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'installation_date' => 'date',
    ];

    /**
     * Get the organization that owns the equipment.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the customer that owns the equipment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the equipment type.
     */
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    /**
     * Get the inspections for the equipment.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
