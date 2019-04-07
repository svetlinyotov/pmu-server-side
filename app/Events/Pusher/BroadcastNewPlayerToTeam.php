<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BroadcastNewPlayerToTeam extends Event implements ShouldBroadcast
{

    use SerializesModels;

    public $userId;
    public $gameId;
    public $name;
    public $email;

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $name
     * @param $email
     */
    public function __construct($userId, $gameId, $name, $email)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->name = $name;
        $this->email = $email;
    }


    public function broadcastOn()
    {
        return ['player-to-team-game-' . $this->gameId . '-channel'];
    }
}