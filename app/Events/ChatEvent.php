<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $ruang_obrolan_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $user, $ruang_obrolan_id)
    {
        $this->message = $message;
        $this->user = $user;
        $this->ruang_obrolan_id = $ruang_obrolan_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PresenceChannel('track.message');
        return new PresenceChannel('ruang.obrolan.' . $this->ruang_obrolan_id);
    }
}
