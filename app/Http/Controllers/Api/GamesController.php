<?php

namespace App\Http\Controllers\Api;


use App\Events\Pusher\BroadcastNewPlayerToTeam;
use App\Events\Pusher\BroadcastRemovePlayerFromTeam;
use App\Events\Pusher\BroadcastTeamGameStart;
use App\Events\Pusher\BroadcastUserLocation;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
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

    public function startSingle(Request $request)
    {
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

    public function startTeamGame(Request $request)
    {
        $gameId = $request->post("gameId");

        $game = Game::find($gameId);
        $game->status = "running";
        $game->save();

        broadcast(new BroadcastTeamGameStart($game->id));

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }

    public function createTeam(Request $request)
    {
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

    public function joinTeam(Request $request)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $gameId = $request->post("gameId");

        $game = Game::findOrFail($gameId);

        $game->users()->syncWithoutDetaching($userId);

        broadcast(new BroadcastNewPlayerToTeam($userId, $game->id, Auth::user()->names, Auth::user()->email));

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }

    public function unJoinTeam(Request $request)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $gameId = $request->post("gameId");

        $game = Game::findOrFail($gameId);

        $game->users()->detach($userId);

        broadcast(new BroadcastRemovePlayerFromTeam($userId, $game->id, Auth::user()->names));

        return response()->json(["gameId" => $game->id, "gameName" => $game->name]);
    }

    public function listTeam(Request $request)
    {
        $locationId = $request->post("locationId");

        $teams = Game::select("id", "name")
            ->where([
                "location_id" => $locationId,
                "status" => "pending"
            ])
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json($teams);
    }

    public function listTeamPlayers(Request $request)
    {
        $gameId = $request->post("gameId");

        $teams = User::select("id", "email", "names")
            ->whereHas('games', function ($q) use ($gameId) {
                $q->where('games.id', $gameId);
            })
            ->get();

        return response()->json($teams);
    }

    public function updateUserLocation(Request $request) {
        $userId = Auth::user()->social_id;
        $userNames = Auth::user()->names;
        $gameId = $request->post("gameId");
        $latitude = $request->post("latitude");
        $longitude = $request->post("longitude");

        broadcast(new BroadcastUserLocation($userId, $userNames, $gameId, $latitude, $longitude));
    }
}
