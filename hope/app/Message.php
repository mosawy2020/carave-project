<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // use HasFactory;
    protected $fillable = [
        'messages', 'receiver_id','receiver_type', 'user_id', 'is_read','image','file'
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sender() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
