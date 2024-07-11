<?php

// Set up WebSocket message handler

date_default_timezone_set("asia/kolkata");

use Workerman\Connection\TcpConnection;

$connections = [];
$offers = [];
$candidates = [];
$answers = [];

function pushIncommingCall($connection, $data)
{
    $incomming = [
        'type' => 'incomming',
        'from' => $data['from'],
        'to' => $data['to'],
        'expires' => $data['expires'],
        'callType' => $data['callType']
    ];
    $connection->send(json_encode($incomming));
}

function remove_offers($from)
{
    global $offers;
    if (array_key_exists($from, $offers)) {
        unset($offers[$from]);
        return true;
    }
}

function remove_candidates($from)
{
    global $candidates;
    if (array_key_exists($from, $candidates)) {
        unset($candidates[$from]);
        return true;
    }
}

$ws_worker->onMessage = function (TcpConnection $connection, $inc) use (&$connections, &$offers, &$candidates, &$answers) {
    // Convert message JSON to array
    $data = json_decode($inc, true);

    // Extract message data
    $type = $data['type'] ?? null;
    $from = $data['from'] ?? null;
    $to = $data['to'] ?? null;

    // Handle connection messages
    if ($type === 'connection') {
        $connections[$data['userId']] = $connection;
        foreach ($offers as $offer) {
            if ($offer['to'] == $data['userId']) {
                print_r($offer);
                $expireTime = ((int) $offer['expires']);
                if (time() > $expireTime) {
                    remove_offers($offer['from']);
                    continue;
                }

                pushIncommingCall($connection, $offer);
                break;
            }
        }
        return;
    }

    // Handle reconnect messages
    if ($type === 'reconnect') {
        print_r($data);
        if (isset($connections[$from])) {
            $connections[$from]->send($inc);
        }
        return;
    }

    // Handle hangup messages
    if ($type === 'hangup') {
        if (isset($connections[$to])) {
            $connections[$to]->send($inc);
        }

        remove_offers($from);
        remove_candidates($from);
        return;
    }

    // call rejected
    if ($type === 'reject') {
        if (isset($connections[$from])) {
            $connections[$from]->send($inc);
        }
        remove_offers($from);
        remove_candidates($from);
        return;
    }

    // Handle offer messages
    if ($type === 'offer') {
        // check if user is actively listening to offers
        foreach ($offers as $offer) {
            if ($offer['to'] == $to || $offer['from'] == $to) {
                $connection->send(json_encode([
                    'type' => 'callstatus',
                    'status' => 'busy'
                ]));
                return;
            }
        }

        $offers[$from] = $data;
        if (isset($connections[$to])) {
            pushIncommingCall($connections[$to], $data);
        }
        return;
    }

    // Handle answer messages
    if ($type === 'answer') {
        $answers[$from] = $data;
        if (isset($connections[$to])) {
            $connections[$to]->send($inc);
        }
        return;
    }

    // Handle candidate messages
    if ($type === 'candidate') {
        $candidates[$from][] = $data;
        $isCaller = $data['caller'];
        if (!$isCaller && isset($connections[$to])) {
            $connections[$to]->send($inc);
        }
        return;
    }

    // Handle fetch messages
    if ($type === 'fetch-offer') {
        // Send pending offers
        if (isset($offers[$from])) {
            $connection->send(json_encode($offers[$from]));
        }
        return;
    }

    if ($type === 'fetch-candidate') {
        // Send pending candidates
        if (isset($candidates[$from])) {
            $connection->send(json_encode($candidates[$from]));
        }
    }
};

// Set up WebSocket connection close handler
$ws_worker->onClose = function (TcpConnection $connection) use (&$connections, &$offers, &$candidates) {
    foreach ($connections as $userId => $conn) {
        if ($conn === $connection) {
            unset($connections[$userId]);
            remove_offers($userId);
            remove_candidates($userId);
            break;
        }
    }
};

// Set up WebSocket error handler
$ws_worker->onError = function (TcpConnection $connection, $code, $msg) {
    // Handle errors, if any
    echo "WebSocket error: $code - $msg\n";
};
