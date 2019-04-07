<?php


namespace App\Events\Pusher;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BroadcastUserLocation extends Event implements ShouldBroadcast
{

    use SerializesModels;

    public $userId;
    public $userNames;
    public $gameId;
    public $latitude;
    public $longitude;

    /**
     * BroadcastUserLocation constructor.
     * @param $userId
     * @param $gameId
     * @param $latitude
     * @param $longitude
     */
    public function __construct($userId, $userNames, $gameId, $latitude, $longitude)
    {
        $this->userId = $userId;
        $this->userNames = $userNames;
        $this->gameId = $gameId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


    public function broadcastOn()
    {
        return ['user-location-game-' . $this->gameId . '-channel'];
    }
}