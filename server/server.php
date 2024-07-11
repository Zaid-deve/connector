<?php

require_once __DIR__ . '/workerman/vendor/autoload.php';

use Workerman\Worker;

// Ensure SSL/TLS context settings are correctly formatted
$context = [
    'ssl' => [
        'local_cert' => 'C:\xampp\apache\conf\ssl.crt\server.crt',
        'local_pk' => 'C:\xampp\apache\conf\ssl.key\server.key',
        'ciphers' => 'HIGH:!aNULL:!MD5',
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
];

// Create a Workerman WebSocket server
// Function to get the local IP address
function getServerIpAddress()
{
    return gethostbyname(gethostname());
}

// Determine server address
$serverIp = getServerIpAddress();
$isLocalhost = ($serverIp === '127.0.0.1') || ($serverIp === '::1');
$ws_server = $isLocalhost ? '0.0.0.0' : $serverIp;
$ws_worker = new Worker("websocket://$ws_server:11001", $context);

// Enable SSL/TLS for secure WebSocket (wss://)
$ws_worker->transport = 'ssl';
$ws_worker->count = 4;

// Load your WebSocket component class
require_once __DIR__ . '/component.php';

// Set up error logging
// Worker::$stdoutFile = __DIR__ . 'workerman.log';

// Run the server
Worker::runAll();
