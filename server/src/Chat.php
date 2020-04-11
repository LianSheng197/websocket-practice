<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // 儲存連線
        $this->clients->attach($conn);
        echo "新連線 ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('連線 %d 送出訊息 "%s" 給其他 %d 個連線' . "\n", $from, $msg, $numRecv);

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // 發送訊息給其他所有連線
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // 連線已結束，刪除
        $this->clients->detach($conn);
        echo "連線 {$conn->resourceId} 已結束\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "錯誤：{$e->getMessage()}\n";
        $conn->close();
    }
}