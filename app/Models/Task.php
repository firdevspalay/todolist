<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;

class Task extends Model
{
    protected $fillable = [
        'title',
        'due_date',
        'is_completed',
        'todo_list_id',
        'assigned_to',
        'assigned_by',
        'assignment_status',
    ];

    public function todoList()
    {
        return $this->belongsTo(TodoList::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->latest();
    }
}