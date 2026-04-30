<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareDevelopment extends Model
{
    protected $table = 'software_development';

    protected $fillable = [
        'request_id',
        'proj_name',
        'brief_desc',
        'prime_obj',
        'features',
        'spec',
        'attachment',
        'proj_deadline',
        'add_info',
    ];

    protected function casts(): array
    {
        return [
            'proj_deadline' => 'datetime',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
