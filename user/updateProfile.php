<?php

require_once "../php/config.php";
require_once "../php/functions.php";
require_once "../db/conn.php";
require_once "user.php";

$response = [
    'Success' => false,
    'UsernameErr' => null,
    'NameErr' => null,
    'ProfileErr' => null,
    'Err' => null
];

$user = new User();
if (!$user->isUserLogedIn()) {
    $response['Err'] = 'LOGIN_ERR';
    echo json_encode($response);
    die();
}
$uid = $user->getUserId();
$oldProfileSrc = $user->getUser($conn, $uid, ['user_profile']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlentities($_POST['username']);
    $name = htmlentities($_POST['name']);
    $profile = $_FILES['profile_img'] ?? null;

    $isUsernameValid = $isNameValid = $isProfileValid = true;
    $isProfileAdded = false;

    // Validate username
    if (!preg_match('/^(?![._])(?!.*\.\.)[a-zA-Z0-9._]{6,16}$/', $username)) {
        $response['UsernameErr'] = 'Username is not valid!';
        $isUsernameValid = false;
    }

    // Validate name
    if (!preg_match('/^[a-zA-Z0-9\s]{0,24}$/', $name)) {
        $response['NameErr'] = 'Name is not valid!';
        $isNameValid = false;
    }

    // Validate profile image
    if ($profile && $profile['error'] == UPLOAD_ERR_OK) {
        if ($profile['name']) {
            $file_ext = pathinfo($profile['name'], PATHINFO_EXTENSION);
            $allowed_exts = ['png', 'jpeg', 'jpg', 'webp'];
            if (!in_array($file_ext, $allowed_exts)) {
                $response['ProfileErr'] = 'Invalid profile image, please use ' . implode(', ', $allowed_exts) . '.';
                $isProfileValid = false;
            } else $isProfileAdded = true;
        } else {
            $response['ProfileErr'] = 'Invalid profile image';
            $isProfileValid = false;
        }
    }

    if ($isProfileValid && $isUsernameValid && $isNameValid) {
        $usernameEncoded = base64_encode($username);
        $nameEncoded = base64_encode($name);

        if ($isProfileAdded) {
            $profileSrc = "{$root}profiles/{$profile['name']}";
            $profileHttpSrc = "{$baseurl}/profiles/{$profile['name']}";
        }
        $qry = "UPDATE users SET user_name = ?, user_cname = ?";
        $params = [$usernameEncoded, $nameEncoded];
        if ($isProfileAdded) {
            $qry .= ",user_profile = ?";
            $params[] = $profileHttpSrc;
        }
        $qry .= " WHERE user_id = ?";
        $params[] = $uid;

        try {
            $stmt = $conn->prepare($qry);
            $stmt->execute($params);
            if ($stmt && $stmt->rowCount()) {
                $response['Success'] = true;
                if ($isProfileAdded) {
                    if (move_uploaded_file($profile['tmp_name'], $profileSrc) && $oldProfileSrc && file_exists($oldProfileSrc)) {
                        unlink("{$root}profiles/" . basename($oldProfileSrc));
                    }
                }
            } else {
                $response['Err'] = 'Failed to update profile';
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') != false) {
                $response['UsernameErr'] = 'Username not available';
            } else {
                $response['Err'] = 'Someting Went Wrong !';
            }
        }
    }
}

$conn = null;
echo json_encode($response);
