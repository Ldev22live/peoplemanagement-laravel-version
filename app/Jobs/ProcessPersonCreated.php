<?php

namespace App\Jobs;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomePersonMail;

class ProcessPersonCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function handle()
    {
        Log::info('Sending email to person: ' . $this->person->id);

        Mail::to($this->person->email)->send(new WelcomePersonMail($this->person));

        Log::info('Email sent to: ' . $this->person->email); 
    }
}
