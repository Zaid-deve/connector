<?php

require_once "php/functions.php";
session_start();
if (getUserId()) {
    header("Location:app/chat.php");
    die();
}

$pagename = 'Home';
require_once "php/config.php";
require_once "includes/head.php";

?>

</head>

<body>

    <!-- BODY -->
    <?php include "{$root}/includes/header.php"; ?>
    <?php include "{$root}/includes/alert.php"; ?>
    <main>
        <div class="container-fluid h-100 d-flex">
            <div class="m-auto text-center">
                <img src="images/couple-video-call-with-phone-free-vector.jpg" alt="#" width="255">
                <div class="text-muted mt-4 tbold tlight">
                    start a video call, <br>
                    audio call <br>
                    to your friends,family....
                </div>
                <a class="btn bdark tlight rounded-5 px-4 py-2 mt-3" href="user/signin.php">GET STARTED</a>
            </div>
        </div>
    </main>




</body>

</html>