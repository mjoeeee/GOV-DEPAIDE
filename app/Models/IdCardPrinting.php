<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdCardPrinting extends Model
{
    protected $table = 'id_card_printing';

    protected $fillable = ['request_id', 'title', 'description', 'details'];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
