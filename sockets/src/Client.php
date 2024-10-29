<?php

namespace App;

class Client
{
    private const SOCKET_PATH = "my.sock";

    public function run() {
        while (true) {
            echo "Enter message: ";
            $input = trim(fgets(STDIN));

            $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
            socket_connect($socket, "my.sock");
            socket_write($socket, $input, strlen($input));

            $response = socket_read($socket, 1024);
            echo "Server response: $response\n";
            socket_close($socket);
        }
    }
}