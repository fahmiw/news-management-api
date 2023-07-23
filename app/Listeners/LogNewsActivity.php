<?php

namespace App\Listeners;

use App\Events\NewsActivity;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogNewsActivity
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\NewsActivity  $event
     * @return void
     */
    public function handle(NewsActivity $event)
    {
        $activity = ucfirst($event->action);

        Log::create([
            'activity' => $activity,
            'entity_type' => 'news',
            'entity_id' => $event->news->id,
            'user_id' => $event->user->id
        ]);
    }
}
