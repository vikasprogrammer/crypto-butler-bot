<?php

namespace App\Console\Commands;

use \App\Messages;
use \App\Tracking;
use Illuminate\Console\Command;

class TrackExchanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:exchanges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track exchanges for users in table "trackings"';

    protected $canned = [
        "EXCHANGE_NOT_SUPPORTED" => "This exchange is not supported",
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function createMessage($username, $message) {
        Messages::create([
            'username' => $username, 
            'message' => $message,
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $trackings = Tracking::active()->get()->where('tracking_type', 'exchange');

        $count = $trackings->count();

        foreach ($trackings as $track) {
            try {
                
                $exchange_name = strtolower($track->exchange_name);
                
                $exchange_class  = "\\ccxt\\$exchange_name";
                $exchange = new $exchange_class([
                    "apiKey" => $track->api_key,
                    "secret" => $track->api_secret,
                ]);

                if($exchange_name == "binance") {
                    $exchange->options['warnOnFetchOpenOrdersWithoutSymbol'] = false;;
                }

                
            } catch (\Throwable $e) {
                echo "Cannot find exchange $exchange_name";

                // $track->status = 0;
                // $track->save();

                // Messages::create([
                //     'username' => $track->username, 
                //     'message' => $this->canned['EXCHANGE_NOT_SUPPORTED'],
                // ]);

                return true;
            }


            /** Open orders tracking starts here */

            $open_orders = $exchange->fetch_open_orders();

            if(is_array($open_orders)) {
                $history = $track->histories()->where("key", "open_orders")->select("value")->get();

                if($history->count() > 0) {
                    $prev_open_orders = unserialize($history[0]->value);
                    //diff the orders and check which orders has disappered, if any. 
                    foreach ($prev_open_orders as $prev_order) {
                        $order_id = $prev_order['id'];
                        // dd($order_id);
                        $search_arr = array_filter($open_orders, function($ar) use($order_id){
                            // dd($ar);
                            // dd($order_id);
                            return ($ar['id'] == $order_id);
                        });

                        // dd($search_arr);

                        if(count($search_arr) == 0 ) {
                            //order has been cancelled or executed. Find out the details. 
                            $order_details = $exchange->fetch_order($order_id, $prev_order['symbol']);
                            $isNotCancelled = $order_details['status'] != "canceled";
                            $isFilled = $order_details['amount'] != $order_details['remaining'];
                            if($isNotCancelled || $isFilled) {
                                $percentFilled = round(100 * ($order_details['filled'] / $order_details['amount']), 2);
                                $showPercentageFilled = '';
                                if($percentFilled != 100) {
                                    $showPercentageFilled = "$percentFilled%";
                                }
                                $msg = "Order ID {$order_details['id']} - {$order_details['type']} {$order_details['side']} order for {$order_details['amount']} {$order_details['symbol']} has been filled $showPercentageFilled";
                                $this->createMessage($track->username, $msg);
                            }
                        }
                    }
                } 
                
                $track->histories()->delete();

                $track->histories()->create([
                    'key' => 'open_orders',
                    'value' => serialize($open_orders)
                ]);
            } else {
                echo "Open orders failed for {$track->username}\n";
            }

            


            

        }
    }
}
