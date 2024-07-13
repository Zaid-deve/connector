<?php

require_once "functions.php";
session_start();
$uid = getUserId();

$response = [
    'Success' => false,
    'Err' => null
];

if (!$uid) {
    $response['Err'] = 'LOGIN_FAILED';
    die(json_encode($response));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "../db/conn.php";
    $username = htmlentities(trim($_POST['username']));
    if (!$username) {
        $response['Err'] = 'Something Went Wrong !';
        die(json_encode($response));
    }


    $enc_username = base64_encode($username);
    $stmt = $conn->prepare("SELECT req_id,sender_user_id sid FROM user_requests WHERE sender_user_id = (SELECT user_id FROM users WHERE user_name = ?) AND recipient_user_id = ?");
    $stmt->execute([$enc_username, $uid]);

    if ($stmt && $stmt->rowCount()) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $reqId = $data['req_id'];
        $senderId = $data['sid'];
        $reqAccepted = false;
        $reqType = $_POST['type'] ?? 'reject';

        if ($reqType === 'accept') {
            $stmt = $conn->prepare("INSERT INTO user_friends(sender_user_id, recipient_user_id) VALUES (?,?)");
            $stmt->execute([$senderId, $uid]);
            if ($stmt && $stmt->rowCount()) {
                $reqAccepted = true;
                $response['Success'] = true;
            }
        }

        if ($reqType == 'reject' || ($reqType == 'accept' && $reqAccepted)) {
            $stmt = $conn->prepare("DELETE FROM user_requests WHERE req_id = ?");
            $stmt->execute([$reqId]);
            if ($stmt && $stmt->rowCount()) {
                $response['Success'] = true;
            } else {
                $response['Err'] = "Failed To {$_POST['type']}";
            }
        }
    } else {
        $response['Err'] = 'Request Already Modified, Accepted,Deleted Or The User Dosent Exists !';
    }

    echo json_encode($response);
}