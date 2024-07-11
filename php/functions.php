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
    $then = new DateTime($timestamp);
    $diff = $now->diff($then);
    $formats = ['year', 'month', 'day', 'hour', 'minute', 'second'];

    $i = 0;
    $output = "";
    foreach ($diff as $d => $v) {
        if ($v > 0) {
            $output = $v . $formats[$i] . ' ago';
            break;
        }

        if ($d == 's') {
            break;
        }
        $i++;
    }
    return $output;
}
