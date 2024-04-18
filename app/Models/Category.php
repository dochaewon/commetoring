<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // Category 모델과 Question 모델 간의 일대다 관계를 정의합니다.
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
