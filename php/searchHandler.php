<?php

require_once "../php/config.php";
require_once "../user/user.php";

$response = [
    'Success' => false,
    'Err' => null,
    'Users' => []
];

if (isset($_POST['qry'])) {
    require_once "../db/conn.php";
    $qry = htmlentities($_POST['qry']);
    if (empty($qry)) {
        die(json_encode($response));
    }
    $enc_qry = base64_encode($qry);

    $stmt = $conn->prepare("SELECT user_profile,user_cname FROM users WHERE user_name = ?");
    $stmt->execute([$enc_qry]);
    if ($stmt) {
        $response['Success'] = true;
        if ($stmt->rowCount()) {
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $name = base64_decode($data['user_cname']);
                $profile = User::getProfileUri($data['user_profile']);
                $peer = $enc_qry;
                $response['Users'][] = ['peer' => $peer, 'name' => $name, 'profile' => $profile];
            }
        }
    }
}

echo json_encode($response);
