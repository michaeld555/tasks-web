<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{

    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'jwt_token',
        'redmine_token',
        'token_expire',
        'user_id',
        'redmine_id',
        'profile_photo',
        'notifications',
        'alerts',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
