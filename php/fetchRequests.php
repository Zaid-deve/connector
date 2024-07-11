<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once "./functions.php";
    require_once "../db/conn.php";

    $response = [
        'Success' => false,
        'Users' => [],
        'Err' => 'Something Went Wrong !'
    ];

    //user
    $uid = getUserId();
    if (!$uid) {
        $response['Err'] = 'LOGIN_FAILED';
        echo json_encode($response);
        die();
    }

    $stmt = $conn->prepare("SELECT DISTINCT user_name, user_cname, user_profile,req_timestamp FROM user_requests JOIN users ON user_requests.sender_user_id = users.user_id WHERE recipient_user_id = ? ORDER BY req_id DESC");
    $stmt->execute([$uid]);
    $response['Success'] = true;
    if($stmt && $stmt->rowCount()){
        while($data = $stmt->fetch(PDO::FETCH_ASSOC)){
            $username = base64_decode($data['user_name']);
            $name = base64_decode($data['user_cname']);
            $profile = $data['user_profile'];
            
            $response['Users'][] = [
                'username' => $username,
                'name' => $name, 
                'profile' => $profile,
                'req_time' => diff($data['req_timestamp'])
            ];
        }
        $response['end'] = $stmt->rowCount();
    }


    // response
    echo json_encode($response);
}
