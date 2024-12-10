<?php

namespace App\Events;

use BeyondCode\LaravelWebSockets\Channels\PrivateChannel as ChannelsPrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifUserEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $user;
    public $event;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $user, $event = null, $data = [])
    {
        $this->user_id = $user_id;
        $this->user = $user;
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notif.user.' . $this->user_id);
    }
}
