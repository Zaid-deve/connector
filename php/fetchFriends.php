<?php

require_once "config.php";
require_once "../user/user.php";

$response = [
    'Success' => false,
    'Users' => [],
    'Err' => null
];

$user = new User();

if (!$user->isUserLogedIn()) {
    $response['Err'] = 'LOGIN_FAILED';
    die(json_encode($response));
}

$uid = $user->getUserId();

require_once "../db/conn.php";

// get user id
$stmt = $conn->prepare("SELECT u.user_name,u.user_cname,u.user_profile,uf.is_star,uf.is_blocked FROM users u
                        JOIN user_friends uf ON u.user_id = uf.sender_user_id OR u.user_id = uf.recipient_user_id
                        WHERE (uf.sender_user_id = ? OR uf.recipient_user_id = ?) AND u.user_id != ?");
$stmt->execute([$uid, $uid, $uid]);

if ($stmt) {
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['Success'] = true;
    foreach ($data as $f) {
        $username = base64_decode($f['user_name']);
        $name = base64_decode($f['user_cname']);
        $profile = $user->getProfileUri($f['user_profile']);
        $isStarFriend = $f['is_star'] ? 1 : 0;
        $isBlockedFriend = $f['is_blocked'] ? 1 : 0;

        $response['Users'][] = [
            'username' => $username,
            'name' => $name,
            'profile' => $profile,
            'isStarFriend' => $isStarFriend,
            'isBlockedFriend' => $isBlockedFriend
        ];
    }
    $response['End'] = $stmt->rowCount();
} else {
    $response['Err'] = 'Failed To Fetch Users !';
}


if (!isset($NO_RETURN)) {
    echo json_encode($response);
}
