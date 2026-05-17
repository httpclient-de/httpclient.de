<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Environment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = ['name', 'variables'];

    protected $casts = [
        'variables' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'variables'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
