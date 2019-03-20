<?php
/**
 * Created by PhpStorm.
 * User: svetlin
 * Date: 20/03/2019
 * Time: 9:54 AM
 */

namespace App\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = "users_tokens";

    protected $fillable = ['user_id', 'token'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}