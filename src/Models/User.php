<?php
// src/Models/User.php

namespace Elkha\ChatBackend\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['username', 'token'];
    protected $hidden = ['created_at', 'updated_at'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
