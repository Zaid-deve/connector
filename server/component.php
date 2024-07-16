<?php

use Workerman\Connection\TcpConnection;

$connections = [];
$offers = [];
$candidates = [];
$answers = [];

$ws_worker->onMessage = function (TcpConnection $tcp, $msg) use (&$connections, &$offers, &$candidates, &$answers) {
    $data = json_decode($msg, true);

    // defaults
    $type = $data['type'];
    $from = $data['from'] ?? null;
    $to = $data['to'] ?? null;
    $recipent = null;
    $recipentStatus = null;


    // recipent
    if (isset($connections[$to])) {
        $recipent = $connections[$to]['connection'];
        $recipentStatus = $connections[$to]['status'];
    }

    // connection
    if ($type === 'connection') {
        $connections[$data['userId']] = ['connection' => $tcp, 'status' => 'free'];
        sendIncommings($data['userId']);
        return;
    }

    // reconnect
    if ($type == 'reconnect') {
        if (isset($connections[$from])) {
            $reconnect = [
                'type' => 'reconnect',
                'to' => $from
            ];
            $connections[$from]['connection']->send(json_encode($reconnect));
        }
        return;
    }

    // call hangup and reject
    if ($type == 'reject') {
        if (isset($connections[$from])) {
            $reject = [
                'type' => 'reject',
                'to' => $from
            ];
            $connections[$from]['connection']->send(json_encode($reject));
        }
        destroyCall($from);
        setStaus($from,'free');
        setStaus($to,'free');
        return;
    }

    // call rejected or hanguped
    if ($type == 'hangup') {
        destroyCall($from);
        destroyCall($to);
        setStaus($from, 'free');
        setStaus($to, 'free');
        $isCaller = $data['isCaller'] ?? null;
        if ($isCaller) {
            $recipent->send($msg);
        } else {
            $hangup = [
                'type' => 'hangup',
                'to' => $from
            ];
            $connections[$from]['connection']->send(json_encode($hangup));
        }

        return;
    }

    // offer
    if ($type === 'offer') {
        if ($recipent && $recipentStatus == 'free') {
            $offers[$from] = $data;
            sendIncommings($to);
            setStaus($from, 'busy');
            setStaus($to, 'busy');
        } else {
            $tcp->send(json_encode(['type' => 'error', 'to' => $from, 'error' => 'user is busy or is un-able to take calls currently !']));
        }
        return;
    }

    // candidates
    if ($type === 'candidate') {
        $candidates[$from] = $data;
        if ($recipent) {
            sendCandidates($from, $recipent);
        }
        return;
    }

    // answers 
    if ($type === 'answer') {
        $answers[$from] = $data;
        if ($recipent) {
            sendAnswer($from, $recipent);
        }
        return;
    }

    // fetch requests
    if ($type === 'fetch-offer') {
        sendOffer($from, $recipent);
        sendCandidates($from, $recipent);
    }
};

$ws_worker->onClose = function (TcpConnection $tcp) use (&$connections) {
    foreach ($connections as $userId => $conn) {
        $c = $conn['connection'];
        if ($c === $tcp) {
            removeOffer($userId);
            removeCandidates($userId);
            removeAnswers($userId);
            break;
        }
    }
};

// ws handlers


function sendIncommings($userId)
{
    global $offers, $connections;
    foreach ($offers as $offer) {
        $expiry = (int) $offer['expires'];
        if ($offer['to'] == $userId && time() < ($expiry / 1000)) {
            $data = $offer;
            $data['type'] = 'incomming';
            unset($data['sdp']);
            $connections[$userId]['connection']->send(json_encode($data));
            break;
        }
    }
}

function sendOffer($userId, $recipent)
{
    global $offers;
    if (isset($offers[$userId])) {
        $recipent->send(json_encode($offers[$userId]));
    }
}

function sendCandidates($userId, $recipent)
{
    global $candidates;
    if (isset($candidates[$userId])) {
        $recipent->send(json_encode($candidates[$userId]));
    }
}

function sendAnswer($userId, $recipent)
{
    global $answers;
    if (isset($answers[$userId])) {
        $recipent->send(json_encode($answers[$userId]));
    }
}

function removeOffer($userId)
{
    global $offers;
    if (isset($offers[$userId])) {
        unset($offers[$userId]);
    }
}

function removeCandidates($userId)
{
    global $candidates;
    if (isset($candidates[$userId])) {
        unset($candidates[$userId]);
    }
}

function removeAnswers($userId)
{
    global $answers;
    if (isset($answers[$userId])) {
        unset($answers[$userId]);
    }
}

function removeConnection($userId)
{
    global $connections;
    if (isset($connections[$userId])) {
        unset($connections[$userId]);
    }
}

function destroyCall($userId)
{
    global $connections;
    if (isset($connections[$userId])) {
        removeOffer($userId);
        removeCandidates($userId);
        removeAnswers($userId);
    }
}

function setStaus($userId, $status)
{
    global $connections;
    if (isset($connections[$userId])) {
        $connections[$userId]['status'] = $status;
    }
}
