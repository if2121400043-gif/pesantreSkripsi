<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;




    protected $table = 'users';
    
    protected $fillable = [
        'orang_id', 'username', 'email', 'password', 'is_active', 'must_change_password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function getActiveRoleAttribute()
    {
        return $this->roles()->where('is_active', true)->where('is_default', true)->first() 
            ?? $this->roles()->where('is_active', true)->first();
    }
}
