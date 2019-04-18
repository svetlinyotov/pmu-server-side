<?php

namespace App\Http\Controllers\Api;


use App\Events\Pusher\BroadcastQRFound;
use App\Http\Controllers\Controller;
use App\Models\Marker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QRController extends Controller
{
    public function foundQR(Request $request)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $gameId = $request->post("gameId");
        $qrCode = $request->post("qrCode");

        $marker = Marker::where("qr_code", $qrCode)->first();

        if ($marker == null) {
            return response()->json(["error" => "MARKER_NOT_FOUND"], 400);
        }

        if (DB::select("SELECT COUNT(*) as count FROM games_markers WHERE game_id = ? AND marker_id = ?", [$gameId, $userId, $marker->id])[0]->count == 0) {
            if (DB::insert("INSERT INTO games_markers (game_id, marker_id, user_id) VALUES (?, ?, ?)", [$gameId, $marker->id, $userId]) == 1) {
                broadcast(new BroadcastQRFound($userId, $gameId, $marker->name, $marker->latitude, $marker->longitude));
                return response()->json($marker, 200);
            }
        } else {
            return response()->json(["error" => "QR_ALREADY_FOUND"], 400);
        }

        return response()->json(["error" => "SERVER_ERROR"], 400);
    }

    public function show($id)
    {
        $info = Marker::where("id", $id)->first();

        if ($info == null) {
            abort(404);
        }

        return view("markers.show", ["info" => $info]);
    }
}
