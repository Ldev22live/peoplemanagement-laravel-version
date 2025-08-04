<?php

namespace App\Listeners;

use App\Events\PersonCreated;
use App\Jobs\ProcessPersonCreated;
use Illuminate\Support\Facades\Log;

class PersonCreatedListener
{
    public function handle(PersonCreated $event)
    {
        Log::info('Listener triggered for person ID: ' . $event->person->id);

        ProcessPersonCreated::dispatch($event->person);
    }
}
