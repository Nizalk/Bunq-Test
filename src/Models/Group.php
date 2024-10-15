<?php
// src/Models/Group.php

namespace Elkha\ChatBackend\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
