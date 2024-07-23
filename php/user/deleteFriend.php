<?php

require_once "../config.php";
require_once "../functions.php";
require_once "../../user/user.php";
require_once "../../db/conn.php";

$response = [
    'Success' => false,
    'Err' => null
];

$user = new User();
if (!$user->isUserLogedIn()) {
    $response['Err'] = 'LOGIN_FAILED';
    die(json_encode($response));
}
$uid = $user->getUserId();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username'])) {
    $username = htmlentities(trim($_POST['username']));
    if (strlen($username) < 6) {
        $response['Err'] = 'Something Went Wrong !';
    } else {
        $remoteUserId = $user->getUser($conn, $username, ['user_id']);
        if ($remoteUserId) {
            $stmt = $conn->prepare("DELETE FROM user_friends WHERE 
                                    (sender_user_id = ? AND recipient_user_id = ?) OR (recipient_user_id = ? AND sender_user_id = ?)");
            $stmt->execute([$uid, $remoteUserId, $uid, $remoteUserId]);
            if ($stmt->rowCount()) {
                $response['Success'] = true;
            } else {
                $response['Err'] = 'No Such User In Your Friend List';
            }
        } else {
            $response['Err'] = 'No Such User In Your Friend List';
        }
    }
}

$conn = null;
echo json_encode($response);
