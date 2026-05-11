<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HttpClientRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'request_collection_id',
        'name',
        'method',
        'url',
        'headers',
        'query_params',
        'body',
        'body_type'
    ];

    protected $casts = [
        'headers' => 'array',
        'query_params' => 'array',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(RequestCollection::class, 'request_collection_id');
    }
}
