<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GamesMarkers extends Model
{
    protected $fillable = ['game_id', 'marker_id', 'user_id'];

    public function questions() {
        return $this->hasMany(TestQuestions::class, 'marker_id', 'marker_id');
    }

}
