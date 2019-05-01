<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TestAnswers extends Model
{
    protected $fillable = ['question_id', 'answer', 'is_correct'];

}
