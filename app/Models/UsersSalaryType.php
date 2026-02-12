<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersSalaryType extends Model
{
    use HasFactory;

    protected $table = 'users_salary_type';

    protected $fillable = [
        'user_id',
        'salary_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
