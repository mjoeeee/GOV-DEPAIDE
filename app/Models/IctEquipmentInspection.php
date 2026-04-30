<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IctEquipmentInspection extends Model
{
    protected $table = 'ict_equipment_inspection';

    protected $fillable = [
        'request_id',
        'item',
        'property_no',
        'receipt_no',
        'acquisition_cost',
        'acquisition_date',
        'complaints',
        'scope_last_repair',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
