<?php

// Function to get the local IP address
function getServerIpAddress()
{
    $localIP = gethostbyname(gethostname());
    return $localIP;
}
echo getServerIpAddress();

// Determine server address
$serverIp = getServerIpAddress();
$isLocalhost = ($serverIp === '127.0.0.1') || ($serverIp === '::1');
$server = $isLocalhost ? '0.0.0.0' : $serverIp;
$port = 11001;
