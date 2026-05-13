<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class IctEquipmentInspection extends Model
{
    protected $table = 'tbl_inspection_depaide';

    protected $fillable = [
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

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_inspection_depaide')) {
            return 'tbl_inspection_depaide';
        }

        return 'ict_equipment_inspection';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
