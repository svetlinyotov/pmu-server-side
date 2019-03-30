<?php

namespace App\Models;

use App\Models\UserToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];

    public static function ranking()
    {
        return DB::select("
            SELECT users_with_markers.id,
                   users_with_markers.email,
                   users_with_markers.names,
                   users_with_markers.points_from_markers,
                   COUNT(ua.id) * 10                         count_of_correct_answers,
                   (points_from_markers + COUNT(ua.id) * 10) total
            FROM (
                 SELECT u.*, COALESCE(SUM(m.points), 0) points_from_markers
                 FROM users u
                    LEFT JOIN markers m ON m.id IN (SELECT gm.marker_id FROM games_markers gm WHERE gm.user_id = u.id)
                    GROUP By u.id
                 ) users_with_markers
            LEFT JOIN users_answers ua
                     ON ua.user_id = users_with_markers.id
                     AND ua.answer_id IN (
                      SELECT id
                      FROM test_answers ta
                      WHERE ta.is_correct = 1
                        AND id = ua.answer_id
                     )
            GROUP BY users_with_markers.id
            ORDER BY total DESC
        ");
    }
}
