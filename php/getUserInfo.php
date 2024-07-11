<?php

$response = [
    'Success' => false,
    'Data' => []
];

require_once "config.php";
require_once "../db/conn.php";
if ($_SERVER['REQUEST_METHOD'] != 'POST' && empty($_POST['user_name'])) {
    die(json_encode($response));
}

$user_name = htmlentities($_POST['user_name']);

if (!base64_decode($user_name, true)) {
    $user_name = base64_encode($user_name);
}
$stmt = $conn->prepare("SELECT user_cname, user_profile FROM users WHERE user_name = ?");
$stmt->execute([$user_name]);
if ($stmt && $stmt->rowCount()) {
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = base64_decode($data['user_cname']);
    $profile = $data['user_profile'];
    $user_name = base64_decode($user_name);

    if (!$profile || !file_exists($root . $profile)) {
        $profile = 'images/main-qimg-6d72b77c81c9main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif841bd98fc806d702e859-lq.jfif';
    }

    $response['Data'] = [
        'user_name' => $user_name,
        'name' => $name,
        'profile' => $baseurl . $profile
    ];
    $response['Success'] = true;
}

echo json_encode($response);
