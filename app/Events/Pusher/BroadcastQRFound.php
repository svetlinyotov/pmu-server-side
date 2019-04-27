<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BroadcastQRFound extends Event implements ShouldBroadcast
{

    use SerializesModels;

    public $userId;
    public $gameId;
    public $name;
    public $latitude;
    public $longitude;
    public $userName;

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $name
     * @param $latitude
     * @param $longitude
     * @param $userName
     */
    public function __construct($userId, $gameId, $name, $latitude, $longitude, $userName)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->userName = $userName;
    }


    public function broadcastOn()
    {
        return ['game-playing-' . $this->gameId . '-channel'];
    }
}
