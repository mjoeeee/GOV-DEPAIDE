<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class SoftwareDevelopment extends Model
{
    protected $table = 'tbl_softdev_depaide';

    protected $fillable = [
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

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_softdev_depaide')) {
            return 'tbl_softdev_depaide';
        }

        return 'software_development';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
