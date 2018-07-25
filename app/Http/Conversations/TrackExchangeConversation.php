<?php

namespace App\Http\Conversations;

use App\Tracking;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TrackExchangeConversation extends Conversation
{

    protected $exchange, $api_key, $api_secret, $alias;

    public function stopsConversation(IncomingMessage $message)
	{
        error_log($message->getText());
		if ($message->getText() == '/stop') {
            // $this->say('Ok. stop.');
			return true;
		}
        // $this->say('Not. stop.');
		return false;
	}

    public function askExchange() {
        $this->say('We will now ask various details to track an exchange account. Enter /stop to reset.');
        $this->ask('Enter the exchange name (Binance, BitTex etc).', function(Answer $answer) {
            // Save result
            $this->exchange = $answer->getText();

            // $this->say('Nice to meet you '.$this->exchange);
            $this->askApi();
        });
    }

    public function askApi() {
        $this->ask('Enter the API Key (readonly)', function(Answer $answer) {
            // Save result
            $this->api_key = $answer->getText();

            $this->ask('Enter the API Secret', function(Answer $answer) {
                // Save result
                $this->api_secret = $answer->getText();
                $this->askAlias();
            });
        });
    }

    public function askAlias() {
        $this->ask('Provide an alias for this setting (my favorite, family fund, etc)', function(Answer $answer) {
            // Save result
            $this->alias = $answer->getText();

            $user = $this->bot->getUser();


            Tracking::create([
                'tracking_type' => 'exchange',
                'exchange_name' => $this->exchange,
                'api_key' => $this->api_key,
                'api_secret' => $this->api_secret,
                'alias' => $this->alias,
                'username' => $user->getId(),
                'status' => 1,
            ]);

            $this->say('Thanks for providing details. We are now tracking this exchange for you.');

        });
        
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askExchange();
    }
}
