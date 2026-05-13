<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IctMaintenanceInspection extends Model
{
    protected $table = 'ict_maintenance_inspection';

    protected $fillable = [
        'type',
        'request_id',
        // Maintenance fields
        'date_current',
        'time_current',
        'req_name',
        'req_designation',
        'req_DO',
        'DOPE',
        'brand',
        'prop_no',
        'serial_no',
        'date_last_repair',
        'defects',
        // Inspection fields
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
            'date_current' => 'date',
            'date_last_repair' => 'date',
            'acquisition_date' => 'date',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
