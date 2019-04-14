<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
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

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $name
     * @param $latitude
     * @param $longitude
     */
    public function __construct($userId, $gameId, $name, $latitude, $longitude)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


    public function broadcastOn()
    {
        return ['game-playing-' . $this->gameId . '-channel'];
    }
}
