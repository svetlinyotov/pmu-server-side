<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
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

    public function startSingle(Request $request) {
        $userId = Auth::user()->getAuthIdentifier();
        $locationId = $request->post("locationId");

        $game = new Game();
        $game->location_id = $locationId;
        $game->name = "Play " . date("d M Y");
        $game->status = "running";
        $game->save();

        $game->users()->sync($userId);

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }

    public function createTeam(Request $request) {
        $userId = Auth::user()->getAuthIdentifier();
        $locationId = $request->post("locationId");
        $teamName = $request->post("name");

        $game = new Game();
        $game->location_id = $locationId;
        $game->name = $teamName;
        $game->status = "pending";
        $game->save();

        $game->users()->sync($userId);

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }

    public function joinTeam(Request $request) {
        $userId = Auth::user()->getAuthIdentifier();
        $gameId = $request->post("gameId");

        $game = Game::findOrFail($gameId);

        $game->users()->attach($userId);

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }
}