<?php

$server = $_SERVER['SERVER_NAME'];
$scheme = $_SERVER['REQUEST_SCHEME'];
$root = $_SERVER['DOCUMENT_ROOT'] . '/connector' . '/'; 

$baseurl = "$scheme://$server/connector/";
