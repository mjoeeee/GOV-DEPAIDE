<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class AudioVisualEditing extends Model
{
    protected $table = 'tbl_audiovisual_depaide';

    protected $fillable = [
        'request_id',
        'title',
        'proj_desc',
        'project_type',
        'music_preference',
        'deliverables',
        'style_tone',
        'delivery_method',
        'project_deadline',
    ];

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_audiovisual_depaide')) {
            return 'tbl_audiovisual_depaide';
        }

        return 'audio_visual_editing';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
