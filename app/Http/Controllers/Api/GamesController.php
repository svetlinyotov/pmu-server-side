<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class GamesController extends Controller
{
    public function ranking()
    {
        return Game::ranking();
    }

    public function rankingPersonal()
    {
        return Game::rankingForUser(Auth::user()->getAuthIdentifier());
    }

    public function show($id)
    {
        $info = Location::where("id", $id)->first();

        if ($info == null) {
            abort(404);
        }

        return view("locations.show", ["info" => $info]);
    }
}