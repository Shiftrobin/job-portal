<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function employer(){
        return $this->belongsTo(User::class, 'employer_id');
    }

}
