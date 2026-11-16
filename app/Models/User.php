<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'school_id', 'phone', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SCHOOL = 'school';

    public const ROLE_BENDAHARA = 'bendahara';

    public const ROLE_OPERATOR = 'operator';

    public const ROLE_TEACHER = 'teacher';

    public const ROLE_STAFF = 'staff';

    public const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_SCHOOL,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === self::ROLE_SCHOOL;
    }

    public function isTreasurer(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function canAccessBilling(): bool
    {
        return $this->isAdmin() || empty($this->role) || in_array($this->role, [self::ROLE_SCHOOL, self::ROLE_BENDAHARA], true);
    }

    public function canManageTeam(): bool
    {
        return $this->isAdmin() || empty($this->role) || $this->role === self::ROLE_SCHOOL;
    }

    public function canManageSchoolProfile(): bool
    {
        return $this->isAdmin() || empty($this->role) || in_array($this->role, [self::ROLE_SCHOOL, self::ROLE_OPERATOR], true);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Administrator Sistem',
            self::ROLE_SCHOOL => 'Kepala Sekolah / Admin Utama',
            self::ROLE_BENDAHARA => 'Bendahara BOS',
            self::ROLE_OPERATOR => 'Operator Dapodik & IT',
            self::ROLE_TEACHER => 'Guru / Pengajar',
            self::ROLE_STAFF => 'Staf Tata Usaha',
            default => ucfirst((string) $this->role),
        };
    }
}
