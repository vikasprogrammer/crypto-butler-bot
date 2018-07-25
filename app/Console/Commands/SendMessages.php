<?php

namespace App\Console\Commands;

use \App\Messages;
use BotMan\BotMan\BotMan; 
use Illuminate\Console\Command;
use BotMan\Drivers\Telegram\TelegramDriver;

class SendMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:send_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
       $messages = Messages::all()->where("status", 0);
       $botman = app('botman');
       
       foreach ($messages as $message) {
        //    echo "sending";
           $botman->say($message->message, $message->username, TelegramDriver::class);
           $message->status = 1;
           $message->save();
       }

       

       
    }
}
