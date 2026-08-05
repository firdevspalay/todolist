<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'body',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->latest();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}