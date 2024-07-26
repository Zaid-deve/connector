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
            // Check if the remote user has already blocked the current user
            $checkStmt = $conn->prepare(
                "SELECT is_blocked FROM user_friends 
                 WHERE 
                 (sender_user_id = :remoteUserId AND recipient_user_id = :uid) OR 
                 (recipient_user_id = :remoteUserId AND sender_user_id = :uid)"
            );
            $checkStmt->execute([
                ":uid" => $uid,
                ":remoteUserId" => $remoteUserId
            ]);
            $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($checkResult && $checkResult['is_blocked'] == $remoteUserId) {
                $response['Err'] = 'Error blocking/unblocking the user!';
            } else {
                $stmt = $conn->prepare(
                    "UPDATE user_friends SET is_blocked = CASE WHEN is_blocked IS NULL THEN :uid ELSE NULL END WHERE
                     (sender_user_id = :uid AND recipient_user_id = :remoteUserId) OR 
                     (recipient_user_id = :uid AND sender_user_id = :remoteUserId)"
                );
                $stmt->execute([
                    ":uid" => $uid,
                    ':remoteUserId' => $remoteUserId
                ]);
                if ($stmt) {
                    $response['Success'] = true;
                } else {
                    $response['Err'] = 'Error blocking/unblocking the user!';
                }
            }
        } else {
            $response['Err'] = 'No Such User In Your Friend List, Or The User Has Been Deleted !';
        }
    }
}

$conn = null;
echo json_encode($response);
?>
