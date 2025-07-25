<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function applications(){
        return $this->hasMany(Application::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
