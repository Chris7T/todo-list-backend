<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function user()
    {
        return $this->belongsTo(TaskList::class);
    }
}
