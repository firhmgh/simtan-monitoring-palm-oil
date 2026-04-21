<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'email', 'password', 'role_id'];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Helper untuk cek akses
    public function hasRole($roleName) {
        return $this->role->name === $roleName;
    }
}