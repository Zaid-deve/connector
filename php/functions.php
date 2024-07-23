<?php

function getUserId()
{
    if (isset($_SESSION['user_id'])) return $_SESSION['user_id'];
    else if (isset($_COOKIE['user_id'])) return $_COOKIE['user_id'];
}

function getCurrentUser($conn)
{
    $uid = getUserId();
    if ($uid) {
        $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ?');
        $stmt->execute([$stmt]);
        if ($stmt && $stmt->rowCount()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    return false;
}

function isBase64($string)
{
    $dec = base64_decode($string, true);
    $isUtf8 = mb_check_encoding($dec, 'UTF-8');
    return $isUtf8 && str_ends_with($string, '=');
}

function diff($timestamp)
{
    $now = new DateTime();
    $givenTime = new DateTime($timestamp);
    $interval = $now->diff($givenTime);
    $diffInSeconds = $now->getTimestamp() - $givenTime->getTimestamp();
    if ($diffInSeconds <= 60 && $diffInSeconds >= 0) {
        return 'just now';
    }

    $timeUnits = [
        'year' => $interval->y,
        'month' => $interval->m,
        'day' => $interval->d,
        'hour' => $interval->h,
        'minute' => $interval->i,
        'second' => $interval->s
    ];

    foreach ($timeUnits as $unit => $value) {
        if ($value > 0) {
            return "$value $unit" . ($value > 1 ? 's' : '') . " ago";
        }
    }

    return 'just now';
}