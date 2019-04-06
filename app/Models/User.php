<?php

namespace App\Models;

use App\Models\UserToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'names', 'email', 'avatar', 'origin', 'social_id'
    ];

    public function tokens()
    {
        return $this->hasMany(UserToken::class);
    }


    public function games()
    {
        return $this->belongsToMany(Game::class, 'users_games');
    }

    public static function isTokenValid(string $origin, string $socialId, string $token): bool
    {
        $countValidUsers = self::select("*")
            ->where(["social_id" => $socialId, "origin" => $origin])
            ->whereHas("tokens", function ($w) use ($token) {
                $w->where("token", $token);
            })->count();

        if ($countValidUsers == 1) {
            return true;
        }

        return false;
    }

    public static function getByHeaders(string $origin, string $socialId, string $token): int
    {
        $user = self::select("users.id")
            ->where(["social_id" => $socialId, "origin" => $origin])
            ->whereHas("tokens", function ($w) use ($token) {
                $w->where("token", $token);
            })->first();

        return ($user->id != null && $user->id > 0) ? $user->id : null;
    }
}
