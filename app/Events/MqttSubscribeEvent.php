<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MqttSubscribeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   /**
     * Create a new event instance.
     */

     public $receivedMessage;
     public $receivedTopic;
     public function __construct($receivedMessage, $receivedTopic)
     {
         $this->receivedMessage = $receivedMessage;
         $this->receivedTopic = $receivedTopic;
     }

     /**
      * Get the channels the event should broadcast on.
      *
      * @return array<int, \Illuminate\Broadcasting\Channel>
      */

     public function broadcastWith(): array
     {
         return [
             'topic' => $this->receivedTopic,
             'message' => $this->receivedMessage
         ];
     }

     public function broadcastOn(): array
     {
         return [
             new Channel('MqttChannel'),
         ];
     }
}
