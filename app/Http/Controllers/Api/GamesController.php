<?php

namespace App\Http\Controllers\Api;


use App\Events\Pusher\BroadcastNewPlayerToTeam;
use App\Events\Pusher\BroadcastRemovePlayerFromTeam;
use App\Events\Pusher\BroadcastTeamGameStart;
use App\Events\Pusher\BroadcastUserLocation;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamesMarkers;
use App\Models\Marker;
use App\Models\TestQuestions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function updateUserLocation(Request $request)
    {
        $userId = Auth::user()->social_id;
        $userNames = Auth::user()->names;
        $gameId = $request->post("gameId");
        $latitude = $request->post("latitude");
        $longitude = $request->post("longitude");

        broadcast(new BroadcastUserLocation($userId, $userNames, $gameId, $latitude, $longitude));
    }

    public function status($id)
    {
        $game = Game::where("id", $id)->first();

        return [
            'name' => $game->name,
            'totalMarkers' => DB::select("SELECT COUNT(*) as count FROM markers WHERE location_id = ?", [$game->location_id])[0]->count,
            'foundMarkers' => DB::select("SELECT COUNT(*) as count FROM games_markers WHERE game_id = ?", [$game->id])[0]->count,
            'totalScore' => DB::select("SELECT SUM(points) as sum FROM markers LEFT JOIN games_markers ON games_markers.marker_id = markers.id WHERE games_markers.game_id = ? AND location_id = ?", [$id, $game->location_id])[0]->sum,
            'foundLocations' => Marker::select("id", "location_id", "name", "photo", "qr_code", "points", "latitude", "longitude")->join("games_markers", "games_markers.marker_id", "=", "markers.id")->where("games_markers.game_id", $id)->where("location_id", $game->location_id)->get()
        ];
    }

    public function getInfoAfterAllMarkersFound($id)
    {
        $game = Game::where("id", $id)->first();

        $timeStart = strtotime($game->created_at);
        $timeEnd = strtotime("now");

        $timeDiff = $timeEnd - $timeStart;

        return [
            'timePlay' => gmdate("H:i:s", $timeDiff),
            'foundMarkers' => DB::select("SELECT COUNT(*) as count FROM games_markers WHERE game_id = ? AND user_id = ?", [$game->id, Auth::user()->id])[0]->count
        ];
    }

    public function generateQuestions($id)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $game = Game::where("id", $id)->first();

        $markers = DB::table("games_markers")->select("marker_id")->where("game_id", $id)->where("user_id", $userId)->get()->pluck("marker_id");

        $data = [];

        foreach ($markers as $marker) {
            $d = TestQuestions::with('answers')->where('marker_id', $marker)->orderByRaw("RAND()")->limit(2)->get();
            foreach ($d as $item) {
                $data[] = $item;
            }
        }

        shuffle($data);

        return $data;
    }

    public function submitTestsAnswers($id, Request $request)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $game = Game::where("id", $id)->first();

        $answers = $request->post('answers');

        $answersIds = explode("|", $answers);

        foreach ($answersIds as $answerId) {
            DB::insert("INSERT INTO users_answers (user_id, game_id, answer_id) VALUES (?, ?, ?)", [$userId, $id, $answerId]);
        }

        response()->json("{}", 200);
    }

    public function finish($id)
    {
        $userId = Auth::user()->getAuthIdentifier();

        DB::update("UPDATE games SET status = 'finished' WHERE id = ?", [$id]);

        return [
            'markersFound' =>
                DB::select("
                    SELECT COUNT(*) markers_count
                    FROM games_markers gm
                    WHERE gm.game_id = ? AND gm.user_id = ?
                    GROUP BY gm.user_id
                ", [$id, $userId])[0]->markers_count,

            'correctAnswers' => DB::select("
                    SELECT COUNT(*) answers_count
                    FROM users_answers ua
                    JOIN test_answers ta ON ta.id = ua.answer_id 
                    WHERE ua.game_id = ? AND ua.user_id = ? AND ta.is_correct = 1
                    GROUP BY ua.user_id
                ", [$id, $userId])[0]->answers_count,

        ];

    }
}
