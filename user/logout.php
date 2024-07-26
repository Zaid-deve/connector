<?php

require_once "user.php";
$user = new User(true);
$user->logout();
header("Location:signin.php");
echo "<p>redirecting please wait...</p>";