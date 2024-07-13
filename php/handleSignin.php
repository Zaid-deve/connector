<?php


require_once "config.php";
require_once "../user/user.php";
$user = new User();

$response = [
    'Success' => false,
    'ErrType' => null,
    'Err' => ''
];

if ($user->isUserLogedIn()) {
    $response['Err'] = "User Already Loged In !";
    die(json_encode($response));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // conn
    require_once "../db/conn.php";

    // data
    $email = htmlentities(trim($_POST['email']));
    $pass = htmlentities(trim($_POST['pass']));

    // validate
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['ErrType'] = 'Email';
        $response['Err'] = "$email Is Not A Valid Email Address";
    } else if (strlen($pass) < 8 || strlen($pass) > 55) {
        $response['ErrType'] = 'Password';
        $response['Err'] = "Password Should Be 8 to 55 Characters Long !";
    } else {
        // encode
        $enc_email = base64_encode($email);
        $enc_pass = base64_encode($pass);

        // qry
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->execute([$enc_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // validate
            if (!$stmt->rowCount()) {
                throw new Exception('NO_USER_FOUND');
            } else {
                if ($user['user_pass'] === $enc_pass) {
                    $response['Success'] = true;
                    $user->setUserId($user['user_id']);
                } else {
                    $response['ErrType'] = "Password";
                    $response['Err'] = "Password Does Not Match !";
                }
            }
        } catch (Exception $e) {
            if ($e->getMessage() === 'NO_USER_FOUND') {
                $stmt = $conn->prepare("INSERT INTO users (user_email,user_pass) VALUES(?,?)");
                $stmt->execute([$enc_email, $enc_pass]);

                if ($conn->lastInsertId()) {
                    $_SESSION['user_id'] = $conn->lastInsertId();
                    $response['Success'] = true;
                }
            } else {
                $response['Err'] = $e->getMessage();
            }
        }
    }
    echo json_encode($response);
}
