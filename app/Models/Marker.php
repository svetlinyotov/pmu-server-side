<?php

namespace App\Models;

use App\Models\UserToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Marker extends Model
{
    protected $fillable = ['name', 'photo', 'qr_code', 'description', 'points', 'latitude', 'longitude'];

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
