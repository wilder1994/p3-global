<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
   
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean', // 👈 nuevo
    ];

    /**
     * Scope para limitar los usuarios que pueden ser responsables de tickets.
     */
    public function scopeResponsables(Builder $query): Builder
    {
        $query->where('is_active', true);

        $roles = config('tickets.responsable_roles', []);

        if (! empty($roles)) {
            $query->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereIn('name', $roles));
        }

        return $query;
    }
}
