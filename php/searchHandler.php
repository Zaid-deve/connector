<?php

require_once "../php/config.php";
require_once "../user/user.php";

$response = [
    'Success' => false,
    'Err' => null,
    'Users' => []
];

$uid = null;
$user = new User();
if ($user->isUserLogedIn()) {
    $uid = $user->getUserId();
}

if (isset($_POST['qry'])) {
    require_once "../db/conn.php";
    $qry = htmlentities($_POST['qry']);
    if (empty($qry)) {
        die(json_encode($response));
    }
    $enc_qry = base64_encode($qry);

    $sql = "SELECT u.user_id,u.user_profile,u.user_cname,uf.is_star,uf.is_blocked,uf.fr_id,ur.req_id FROM users u
            LEFT JOIN user_friends uf ON (u.user_id = uf.sender_user_id OR u.user_id = uf.recipient_user_id) AND (uf.sender_user_id = :current_user OR uf.recipient_user_id = :current_user)
            LEFT JOIN user_requests ur ON (u.user_id = ur.sender_user_id OR u.user_id = ur.recipient_user_id) AND (ur.sender_user_id = :current_user OR ur.recipient_user_id = :current_user)
            WHERE u.user_name = :target_user";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':current_user' => $uid,
        ':target_user' => $enc_qry
    ]);

    if ($stmt) {
        $response['Success'] = true;
        if ($stmt->rowCount()) {
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $name = base64_decode($data['user_cname']);
                $profile = User::getProfileUri($data['user_profile']);
                $peer = $enc_qry;
                $isFriend = $data['fr_id'] || false;
                $isRequested = $data['req_id'] || false;
                $isStar = $data['is_star'] || false;
                $isBlocked = $data['is_blocked'] || false;

                $d = compact('peer', 'name', 'profile', 'isFriend', 'isRequested', 'isStar', 'isBlocked');
                $response['Users'][] = $d;
            }
        }
    }
}

echo json_encode($response);
