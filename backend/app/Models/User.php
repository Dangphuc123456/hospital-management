<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các trường có thể gán giá trị hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_active',
        'role_id',
    ];

    /**
     * Các trường sẽ bị ẩn khi trả về JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kiểu dữ liệu cho các trường.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Quan hệ: User thuộc về một vai trò (role).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Quan hệ: User có thể là một bệnh nhân.
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Quan hệ: User có thể là một bác sĩ.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Quan hệ: User có thể là một nhân viên.
     */
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
}
