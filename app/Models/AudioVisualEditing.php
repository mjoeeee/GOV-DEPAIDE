<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioVisualEditing extends Model
{
    protected $table = 'audio_visual_editing';

    protected $fillable = ['request_id', 'title', 'description', 'details'];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
