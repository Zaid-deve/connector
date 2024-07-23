<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    require_once "./functions.php";
    require_once "../user/user.php";
    require_once "../db/conn.php";

    $response = [
        'Success' => false,
        'Users' => [],
        'Err' => null
    ];

    //user
    $user = new User();
    $uid = $user->getUserId();

    if (!$uid) {
        $response['Err'] = 'LOGIN_FAILED';
        echo json_encode($response);
        die();
    }

    $end = 0;
    $limitStmt = "";
    $params = [$uid];
    $qry = "SELECT DISTINCT req_id,user_name, user_cname, user_profile,req_timestamp FROM user_requests JOIN users ON user_requests.sender_user_id = users.user_id WHERE recipient_user_id = ? ORDER BY req_id DESC";
    if (isset($_GET['end'])) {
        $end = (int) htmlentities($_GET['end']);
        $qry .= " LIMIT $end,10";
    }

    $stmt = $conn->prepare($qry);
    $stmt->execute($params);
    if ($stmt) {
        if ($stmt->rowCount()) {
            $response['Success'] = true;
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $username = base64_decode($data['user_name']);
                $name = base64_decode($data['user_cname']);
                $profile = $user->getProfileUri($data['user_profile']);

                $response['Users'][] = [
                    'peer' => $data['user_name'],
                    'username' => $username,
                    'name' => $name,
                    'profile' => $profile,
                    'req_time' => diff($data['req_timestamp'])
                ];
            }
            $response['end'] = $end + $stmt->rowCount();
        } else {
            $response['Err'] = 'No Requests Pending !';
        }
    } else {
        $response['Err'] = 'Something Went Wrong !';
    }

    // response
    echo json_encode($response);
}
