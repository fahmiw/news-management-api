<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $with = ['user_creator'];

    protected $fillable = [
        'title',
        'content',
        'image_name',
        'user_id',
    ];

    public function user_creator() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
