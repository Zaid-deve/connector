<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "../../php/config.php";
    require_once "../../db/conn.php";
    require_once "../../user/user.php";

    $user = new User();

    $response = [
        'Success' => false,
        'Err' => null
    ];

    if (!$user->isUserLogedIn()) {
        $response['Err'] = 'LOGIN_ERR';
        echo json_encode($response);
        die();
    }

    $uid = $user->getUserId();

    try {
        $conn->beginTransaction();

        // Delete user from user_friends table
        $stmt = $conn->prepare("DELETE FROM user_friends WHERE sender_user_id = :uid OR recipient_user_id = :uid");
        $stmt->execute([':uid' => $uid]);

        // Delete user from user_requests table
        $stmt = $conn->prepare("DELETE FROM user_requests WHERE sender_user_id = :uid OR recipient_user_id = :uid");
        $stmt->execute([':uid' => $uid]);

        // Delete user from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :uid");
        $stmt->execute([':uid' => $uid]);

        $conn->commit();

        $user->logout();
        $response['Success'] = true;
    } catch (Exception $e) {
        $conn->rollBack();
        $response['Err'] = 'DELETE_ERR';
    }

    echo json_encode($response);
}
