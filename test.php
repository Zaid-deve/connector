
<?php

require_once "db/conn.php";
require_once "user/user.php";
$user = new User();
$data = $user->getUser($conn, $user->getUserId(),['user_email','user_pass']);
print_r($data);