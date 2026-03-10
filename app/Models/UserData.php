<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    // Explicitly define the correct table name
    protected $table = 'users_data';

    protected $fillable = [
        'name', 'email', 'age'
    ];
}