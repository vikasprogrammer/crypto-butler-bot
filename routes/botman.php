<?php
use App\Http\Controllers\BotManController;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

$botman = resolve('botman');
$commands = config('commands');

foreach ($commands as $cmd => $cmd_details) {
    $method_name = str_replace("/", "", $cmd);
    $method_name = ucwords(str_replace("_", " ", $method_name));
    $method_name = str_replace(" ", "", $method_name);
    $botman->hears($cmd, BotManController::class.'@'.$method_name);
}

$botman->hears('/start', function ($bot) {
    $bot->reply('Welcome to Crypto Butler! Type /help to get list of commands');
});

$botman->hears('/help', function ($bot) {
    $commands = config('commands');

    $reply = "Here are the list of supported commands" . PHP_EOL . PHP_EOL;
    error_log(var_export($commands,true));
    foreach ($commands as $cmd => $cmd_details) {
        $reply .= $cmd." - ".$cmd_details['help_text'] . PHP_EOL;
    }
    $bot->reply($reply);

});

// $botman->hears('Start conversation', BotManController::class.'@startConversation');

// $botman->fallback(function($bot) {
//     $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
// });