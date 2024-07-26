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
        if (isset($connections[$data['userId']])) {
            updateStatus($data['userId'], 'active', $tcp);
        } else {
            $connections[$data['userId']] = ['connection' => $tcp, 'status' => 'active'];
        }
        sendIncommings($data['userId']);
        return;
    }

    // update status
    if ($type == 'updateStatus') {
        if (isset($connections[$data['userId']])) {
            $status = $data['status'];
            updateStatus($tcp, $data['userId'], $status);
        }
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
        setStatus($from, 'active');
        setStatus($to, 'active');
        return;
    }

    // call rejected or hanguped
    if ($type == 'hangup') {
        destroyCall($from);
        destroyCall($to);
        setStatus($from, 'active');
        setStatus($to, 'active');
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
        if ($recipent && $recipentStatus != 'busy') {
            $offers[$from] = $data;
            sendIncommings($to);
            setStatus($from, 'busy');
            setStatus($to, 'busy');
        } else {
            $tcp->send(json_encode(['type' => 'error', 'to' => $from, 'error' => base64_decode($data['userId']) . " is unable to answer this call currently"]));
        }
        return;
    }

    // candidates
    if ($type === 'candidate') {
        $candidates[$from] = $data;
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

function setStatus($userId, $status)
{
    global $connections;
    if (isset($connections[$userId])) {
        $connections[$userId]['status'] = $status;
    }
}

function disconnectUser($tcp, $userId)
{
    if ($tcp) $tcp->close();
    removeOffer($userId);
    removeCandidates($userId);
    removeAnswers($userId);
    removeConnection($userId);
}

function updateStatus($userId, $status, $tcp = null)
{
    global $connections;
    if ($status == 'disconnect') {
        disconnectUser($tcp, $userId);
        return;
    }

    if ($status == 'active') {
        $connections[$userId]['connection'] = $tcp;
        setStatus($userId, $status);
        return;
    }

    if ($status == 'in-active') {
        setStatus($userId, $status);
    }
}
