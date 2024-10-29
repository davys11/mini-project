<?php

namespace App;

class Client
{
    private const SOCKET_PATH = "my.sock";

    public function run() {
        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);

        if (!socket_connect($socket, "my.sock")) {
            echo "Не удалось подключиться к серверу\n";
            return;
        }

        echo "Подключено к серверу. Введите сообщение ('qq' для выхода):\n";

        do {
            echo "Enter message: ";
            $input = trim(fgets(STDIN));

            socket_write($socket, $input, strlen($input));

            // Чтение ответа от сервера
            $response = socket_read($socket, 1024);
            echo "Server response: $response\n";

        } while ($input !== 'by');

        socket_close($socket);
        echo "Соединение закрыто.\n";
    }
}