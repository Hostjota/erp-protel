<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telefone',
        'nif',
        'morada',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Mapeamento dos níveis hierárquicos (maior número = mais privilégios)
    public function getRoleLevel(): int
    {
        $hierarchy = [
            'user' => 1,
            'admin' => 2,
            'superadmin' => 3,
        ];

        return $hierarchy[$this->role] ?? 1;
    }

    // Verifica se o utilizador tem pelo menos um determinado nível hierárquico
    public function hasRole($role)
    {
        // Lógica hierárquica que criámos anteriormente
        $levels = [
            'user' => 1,
            'admin' => 2,
            'superadmin' => 3,
        ];

        $userLevel = $levels[$this->role] ?? 1;
        $requiredLevel = $levels[$role] ?? 1;

        return $userLevel >= $requiredLevel;
    }
}