<?php

namespace App;

class Server
{
    private const PATH_SOCKET = 'my.sock';
    private $socket;

    private $connection;
    public function run()
    {
        try {
            $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
            socket_bind($this->socket, self::PATH_SOCKET);
            $result = socket_listen($this->socket);

            $this->connection = socket_accept($this->socket);
            if(!$this->connection) throw new \Exception('Чет не законнектился');
            echo 'Походу все оке, жду сообщение';
            do {
                $message = socket_read($this->connection, 1024);
                echo $message . PHP_EOL;
            } while ($message == "qq");
        } catch (\Exception $e) {
            echo 'Лень расписывать по методам и исключениям, поэтому пока так';
        } finally {
            if($this->connection) socket_close($this->connection);
            if($this->socket) socket_close($this->socket);
        }

    }
}