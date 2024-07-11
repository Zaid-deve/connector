<?php

session_start();
require_once "functions.php";

$uid = getUserId();
$response = [
    'Success' => false,
    'IdErr' => null,
    'NameErr' => null,
    'ProfileErr' => null,
    'Err' => null
];

if (!$uid) {
    $response['Err'] = "LOGIN_FAILED";
    echo json_encode($response);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username'])) {
    require_once "config.php";
    require_once "../db/conn.php";
    // data
    $username = htmlentities(trim($_POST['username']));
    $name = htmlentities(trim($_POST['name']));


    $profileImgValid = true;
    if (isset($_FILES['profile'])) {
        $profile = $_FILES['profile'];

        if ($profile['error'] === UPLOAD_ERR_OK) {
            $type = pathinfo($profile['name'], PATHINFO_EXTENSION);
            $formats = ['png', 'jpeg', 'jpg', 'webp', 'gif'];
            if (!array_search($type, $formats)) {
                $profileImgValid = true;
                $response['ProfileErr'] = 'Invalid Image, Image Should Be ' . implode(',', $formats);
                json_encode($response);
            }
        }
    }

    // validate
    $usernameValid = preg_match("/^(?![._])(?!.*\.\.)[a-zA-Z0-9._]{6,16}$/", $username);
    if ($name) {
        $nameValid = preg_match("/^[a-zA-Z0-9\s]{0,24}$/", $name);
    } else $nameValid = true;

    if (!$usernameValid) {
        $response['IdErr'] = "Username Cannot Contain Special Characters And Whitespace, Length Can Be 4-16 Characters !";
    }

    if ($name && !$nameValid) {
        $response['NameErr'] = "Name Can Only Contains Characters And WhiteSpace, Length Can be 4-16";
    }

    if ($profileImgValid && $usernameValid && $nameValid) {
        // set profile
        $enc_username = base64_encode($username);
        $enc_name = "";
        if ($name) $enc_name = base64_encode($name);

        $qry = "UPDATE users SET user_name = ?, user_cname = ?";
        $params = [$enc_username, $enc_name];

        if ($profileImgValid && $profile['tmp_name']) {
            $uploadProfileSrc = 'profiles/' . $profile['name'];
            $qry .= ",user_profile = ?";
            $params[] = $uploadProfileSrc;
        }
        $params[] = $uid;

        try {
            $stmt = $conn->prepare("$qry WHERE user_id = ?");
            $stmt->execute($params);

            if ($stmt && $stmt->rowCount()) {
                $response['Success'] = true;

                if ($profileImgValid && isset($profile['tmp_name'])) {
                    move_uploaded_file($profile['tmp_name'], '../' . $uploadProfileSrc);
                }
            } else {
                $response['Err'] = 'An Expected Error Encountered, Please Try Again !';
            }
        } catch (Exception $e) {
            if ($e->getCode() == 1062 || $e->getCode() == 23000) {
                $response['IdErr'] = 'Username already exists !';
            } else {
                $response['IdErr'] = "Something went wrong !, [Err " . $e->getCode() . "]";
            }
        }
    }

    echo json_encode($response);
}
