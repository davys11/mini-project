<?php

namespace App;

class Server
{
    private const PATH_SOCKET = 'my.sock';
    private $socket;

    public function run()
    {
        try {
            if (file_exists(self::PATH_SOCKET)) {
                unlink(self::PATH_SOCKET);
            }

            $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
            socket_bind($this->socket, self::PATH_SOCKET);
            socket_listen($this->socket);

            echo 'Сервер запущен и ждет подключений...' . PHP_EOL;


            $connection = socket_accept($this->socket);
            if (!$connection) {
                throw new \Exception('Ошибка подключения клиента.');
            }

            echo 'Подключение клиента установлено.' . PHP_EOL;

            // Обрабатываем сообщения клиента
            do {
                $message = socket_read($connection, 1024);
                echo "Получено сообщение: $message" . PHP_EOL;

                $response = "Сервер получил: $message";
                socket_write($connection, $response, strlen($response));
            } while ($message !== 'by');

            // Закрываем соединение, но не основной сокет
            socket_close($connection);


        } catch (\Exception $e) {
            echo 'Ошибка: ', $e->getMessage(), PHP_EOL;
        } finally {
            if ($this->socket) socket_close($this->socket);
            if (file_exists(self::PATH_SOCKET)) unlink(self::PATH_SOCKET);
        }
    }
}