<?php

$response = [
    'Success' => false,
    'Users' => [],
    'Err' => null
];

if(!session_id()) session_start();
require_once "functions.php";
$uid = getUserId();
if (!$uid) {
    $response['Err'] = 'LOGIN_FAILED';
    die(json_encode($response));
}


require_once "../db/conn.php";

// get user id
$stmt = $conn->prepare("SELECT u.user_name,u.user_cname,u.user_profile FROM users u
                            JOIN user_friends uf ON u.user_id = uf.sender_user_id OR u.user_id = uf.recipient_user_id
                            WHERE (uf.sender_user_id = ? OR uf.recipient_user_id = ?) AND u.user_id != ?");
$stmt->execute([$uid, $uid, $uid]);

if ($stmt) {
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['Success'] = true;
    foreach ($data as $f) {
        $username = base64_decode($f['user_name']);
        $name = base64_decode($f['user_cname']);
        $profile = $f['user_profile'];

        $response['Users'][] = [
            'username' => $username,
            'name' => $name,
            'profile' => $profile ?? 'images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif'
        ];
    }
    $response['End'] = $stmt->rowCount();
} else {
    $response['Err'] = 'Failed To Fetch Users !';
}


if (!isset($NO_RETURN)) {
    echo json_encode($response);
}