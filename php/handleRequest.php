<?php

require_once "config.php";
require_once "functions.php";
require_once "../db/conn.php";
require_once "../user/user.php";
$user = new User();

$response = [
    'Success' => false,
    'IdErr' => null,
    'Err' => null
];


if (!$user->isUserLogedIn()) {
    $response['Err'] == 'LOGIN_FAILED';
    echo json_encode($response);
    die();
}
$uid = $user->getUserId();

if ($_SERVER['REQUEST_METHOD'] === "POST" && !empty($_POST['userId'])) {
    // conn


    $userId = htmlentities(trim($_POST['userId']));
    $userId = substr($userId, 0, 16);

    // validate
    if (strlen($userId) < 6) {
        $output = "No User Found For $userId";
    } else {

        // enc id
        $enc_username = base64_encode($userId);

        // query
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_name = ?");
        $stmt->execute([$enc_username]);
        if ($stmt && $stmt->rowCount()) {
            $resUserId = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
            if ($resUserId == $uid) {
                $response['IdErr'] = 'Cannot Send Request To Own Account !';
            } else {
                $stmt = $conn->prepare("SELECT fr_id FROM user_friends WHERE fr_sender_id = ? AND fr_res_id = ?");
                $stmt->execute([$uid, $resUserId]);
                if ($stmt && $stmt->rowCount()) {
                    $response['IdErr'] = "$userId Is Already In Your Friend List Or Request List";
                } else {
                    $stmt = $conn->prepare("INSERT INTO user_requests(sender_user_id,recipient_user_id) VALUES (?,?)");
                    $stmt->execute([$uid, $resUserId]);
                    if ($stmt && $stmt->rowCount()) {
                        $response['Success'] = true;
                    } else {
                        $response['IdErr'] = "Something Went Wrong,Please Try Again !";
                    }
                }
            }
        } else {
            $response['IdErr'] = "No User Found For " . $userId;
        }
    }

    echo json_encode($response);
}
