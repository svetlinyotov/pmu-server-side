<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BroadcastTeamGameStart extends Event implements ShouldBroadcast
{

    use SerializesModels;

    public $gameId;

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $name
     * @param $email
     */
    public function __construct($gameId)
    {
        $this->gameId = $gameId;
    }


    public function broadcastOn()
    {
        return ['player-to-team-game-' . $this->gameId . '-channel'];
    }
}