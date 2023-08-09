<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'task_list_id',
        'title',
        'description',
        'completed',
        'google_token',
        'date_time'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }
}
