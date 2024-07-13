<?php

// response 
$response = [
    'Success' => false,
    'IdErr' => null,
    'Err' => null,
];

require_once "config.php";
require_once "../db/conn.php";
require_once "../user/user.php";

$user = new User();

if (!$user->isUserLogedIn()) {
    $response['Err'] = 'LOGIN_FAILED';
    die(json_encode($response));
}

$uid = $user->getUserId();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userId'])) {
    $resUserName = htmlentities(trim($_POST['userId']));

    // Find user
    $resUserId = $user->getUser($conn, base64_encode($resUserName), ['user_id']);
    if ($resUserId) {
        if ($resUserId == $uid) {
            $response['IdErr'] = "Cannot send request to the same account!";
        } else {
            // Check if they are already friends
            $stmt = $conn->prepare("SELECT fr_id FROM user_friends WHERE (sender_user_id = ? AND recipient_user_id = ?) OR (recipient_user_id = ? AND sender_user_id = ?)");
            $stmt->execute([$uid, $resUserId, $resUserId, $uid]);

            if ($stmt->rowCount()) {
                $response['IdErr'] = 'User is already in your friend list!';
            } else {
                // Check for existing friend requests
                $stmt = $conn->prepare("SELECT req_id FROM user_requests WHERE (sender_user_id = ? AND recipient_user_id = ?) OR (recipient_user_id = ? AND sender_user_id = ?)");
                $stmt->execute([$uid, $resUserId, $resUserId, $uid]);

                if ($stmt->rowCount()) {
                    $response['IdErr'] = 'A friend request is already pending!';
                } else {
                    // Insert new friend request
                    $stmt1 = $conn->prepare("INSERT INTO user_requests (sender_user_id, recipient_user_id) VALUES (?, ?)");
                    $stmt1->execute([$uid, $resUserId]);
                    if ($stmt1->rowCount()) {
                        $response['Success'] = true;
                    } else {
                        $response['Err'] = 'Something went wrong while sending the request!';
                    }
                }
            }
        }
    } else {
        $response['IdErr'] = 'No user found for ' . $resUserName;
    }

    echo json_encode($response);
}
