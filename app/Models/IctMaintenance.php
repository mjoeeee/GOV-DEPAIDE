<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IctMaintenance extends Model
{
    protected $table = 'ict_maintenance';

    protected $fillable = [
        'request_id',
        'date_current',
        'time_current',
        'req_name',
        'req_designation',
        'req_DO',
        'DOPE',
        'brand',
        'prop_no',
        'serial_no',
        'last_repair_date',
        'defects',
    ];

    protected function casts(): array
    {
        return [
            'date_current' => 'date',
            'last_repair_date' => 'date',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
