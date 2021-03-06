<?php

namespace App\Models;

use App\Models\UserToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Location extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];

    public function markers() {
        return $this->hasMany(Marker::class);
    }
}
