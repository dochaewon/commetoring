<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'content', 'category_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

