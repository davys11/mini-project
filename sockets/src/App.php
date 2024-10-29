<?php

namespace App;

class App
{
    public function run()
    {
        global $argv;
        if (isset($argv[1])) {
            if($argv[1] == 'client') {
                $socket = new Client();
            } elseif($argv[1] == 'server') {
                $socket = new Server();
            } else throw new \Exception("No find argv");

            $socket->run();

        } else {
            throw new \Exception("You need input 'Server' or 'Client' when you start script");
        }
    }
}