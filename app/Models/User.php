<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    private const ADMIN_ROLES = [
        'admin',
        'system admin',
    ];

    protected $table = 'tbl_user';

    protected $primaryKey = 'userId';

    public $timestamps = false;

    protected $fillable = [
        'fullname',
        'firstname',
        'lastname',
        'extname',
        'email',
        'password',
        'job_title',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array(
            mb_strtolower(trim((string) $this->role)),
            self::ADMIN_ROLES,
            true
        );
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'userId', 'userId');
    }
}
