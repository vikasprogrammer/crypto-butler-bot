<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
// use App\Conversations\ExampleConversation;
use App\Http\Conversations\TrackExchangeConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    public function tinker()
    {
        return view('tinker');
    }

    public function supportedExchanges(Botman $bot) {
        $bot->reply("Binance, Bittrex, Bitfinex, Hitbtc, Okex, Huobi, Kraken, Coinbase, Poloneix (more to come)");
    }

    public function trackExchange(BotMan $bot)
    {
        $conversation = new TrackExchangeConversation;
        $conversation->setBot($bot);
        $bot->startConversation($conversation);
    }
}
