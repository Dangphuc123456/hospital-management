<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;
    protected $table = 'email_verifications';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'token',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
