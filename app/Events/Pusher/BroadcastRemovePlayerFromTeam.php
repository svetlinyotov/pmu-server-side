<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BroadcastRemovePlayerFromTeam extends Event implements ShouldBroadcast
{

    use SerializesModels;

    public $userId;
    public $gameId;
    public $name;

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $name
     */
    public function __construct($userId, $gameId, $name)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->name = $name;
    }


    public function broadcastOn()
    {
        return ['player-to-team-game-' . $this->gameId . '-channel'];
    }
}
