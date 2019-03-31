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

    public static function rankingForUser($user_id)
    {
        return DB::select("
            SELECT users_with_markers.social_id as user_id,
                   users_with_markers.game_id,
                   users_with_markers.game_name,
                   users_with_markers.location_id,
                   users_with_markers.location_name,
                   users_with_markers.email,
                   users_with_markers.names,
                   users_with_markers.points_from_markers,
                   COUNT(ua.id) * 10                         points_from_correct_answers,
                   (points_from_markers + COUNT(ua.id) * 10) total
            FROM (
                 SELECT u.*, g.id game_id, g.name game_name, l.id location_id, l.name location_name, COALESCE(SUM(m.points), 0) points_from_markers
                 FROM games g
                        LEFT JOIN users u ON u.id IN (SELECT ug.user_id FROM users_games ug WHERE ug.game_id = g.id)
                        LEFT JOIN markers m ON m.id IN (SELECT gm.marker_id FROM games_markers gm WHERE gm.user_id = u.id AND gm.game_id = g.id)
                        LEFT JOIN locations l ON l.id = g.location_id
                 WHERE u.id = ?
                 GROUP BY g.id
                 ) users_with_markers
                   LEFT JOIN users_answers ua
                     ON ua.game_id = users_with_markers.game_id
                          AND ua.user_id = users_with_markers.id
                          AND ua.answer_id IN (
                                              SELECT id
                                              FROM test_answers ta
                                              WHERE ta.is_correct = 1
                                                AND id = ua.answer_id
                                              )
            GROUP BY users_with_markers.game_id
            ORDER BY total DESC
        ", [$user_id]);
    }

    public static function ranking()
    {
        return DB::select("
            SELECT users_with_markers.social_id id,
                   users_with_markers.email,
                   users_with_markers.names,
                   users_with_markers.points_from_markers,
                   COUNT(ua.id) * 10                         points_from_correct_answers,
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
            GROUP BY users_with_markers.social_id
            ORDER BY total DESC
        ");
    }
}
