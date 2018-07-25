## What is it?

A telegram bot which can monitor your exchange account and notify you changes like order executions and wallet deposits/notifications. 

## How to use?

Simply talk to this bot [@crypto_butler_bot](!https://t.me/crypto_butler_bot) and follow the instructions, they are pretty self explainatory. 

## Run locally

You can run the bot locally or on a server. Its pretty simple. 

I have used Laravel and [Botman Studio!](https://botman.io/2.0/botman-studio).

1. Clone the repo
2. edit the .env file with your bot token and database details. 
3. run `php artisan migrate`
4. if you are running locally you will have to obtain a public url using `ngrok`. 
5. run `php artisan botman:telegram:register` to register the public url. make sure to use "https//publicurl.com/botman" (`botman` is important at the end)

