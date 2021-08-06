<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;


class Article extends Model
{
    // use HasFactory;
    protected $table='articles';

    // public function author(){
    //     return $this->belongsTo(User::class);
    // }

    // public function comment(){
    //     return $this->hasMany(Comment::class);
    // }
}
