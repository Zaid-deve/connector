<?php

$response = [
    'Success' => false,
    'Err' => null,
    'Data' => []
];

require_once "config.php";
require_once "../user/user.php";
require_once "../db/conn.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = htmlentities($_POST['username']);
    if (!$username || strlen($username) < 6) {
        die(json_encode($response));
    }

    $user = new User();
    $data = $user->getUser($conn, $username, ['user_cname', 'user_profile']);
    if($data){
        $response['Success'] = true;
        $response['Data'] = [
            'username' => base64_decode($username),
            'name' => base64_decode($data['user_cname']),
            'profile' => $user::getProfileUri($data['user_profile'])
        ];
    } else $response['Err'] = 'No user found !';
}

echo json_encode($response);
$conn = null;
