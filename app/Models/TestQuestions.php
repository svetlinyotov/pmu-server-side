<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TestQuestions extends Model
{
    protected $fillable = ['marker_id', 'question'];

    public function answers() {
        return $this->hasMany(TestAnswers::class, 'question_id');
    }

}
