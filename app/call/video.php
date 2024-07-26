<?php

// get details
$peer = htmlentities($_GET['userId']) ?? null;
if (!$peer) {
    header("Location:../call.php");
    die('Something Went Wrong !');
}

require_once "../../php/config.php";
require_once "../../user/user.php";
require_once "../../db/conn.php";
require_once "../../php/functions.php";

if (!base64_decode($peer)) {
    $peer = base64_encode($peer);
}

// local user
$user = new User(true);
$uid = $user->getUserId();
$localUser = $user->getUser($conn, $uid, ['user_name']);

if (!$localUser) {
    die("Account Configuration Error, Please Try Login Again !");
}

// remote user
$data = $user->getUser($conn, $peer, ['user_name', 'user_cname', 'user_profile']);
if ($data) {
    $remoteUser = $data['user_name'];
    $remoteUserCname = $data['user_cname'];
    $remoteUserProfile = $user->getProfileUri($data['user_profile']);
} else {
    die('End User Not Found Or Is Not Able To Take Calls !');
}

require_once "../../includes/head.php";
?>
<link rel="stylesheet" href="../../styles/call.css">
<link rel="stylesheet" href="../../styles/video.css">
<link rel="stylesheet" href="../../styles/alert.css">
<link rel="stylesheet" href="../../styles/popups.css">
</head>

<body>

    <!-- BODY -->
    <?php 
    include "../../includes/loader.php";
    require_once "../../includes/alert.php"
     ?>
    <div class="container-fluid p-0 ui-box">
        <div class="d-flex flex-column" style="height: 100vh;">
            <?php require_once "../../includes/callHeader.php" ?>

            <div class="flex-grow-1 position-relative" id="videoContainer">
                <div class="h-100 remote-stream-container">
                    <div class="h-100 waiting-box d-flex">
                        <div class="m-auto text-center">
                            <div class="m-auto text-center">
                                <div class="container-icon bg-dark rounded-circle m-auto">
                                    <i class="ri-camera-line text-light"></i>
                                </div>
                                <h3 class="mt-3 mb-0">Video Call</h3>
                                <small class="text-muted">Your Call Is End-To-End Encrypted <br> waiting for the call to connect</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="position-absolute bottom-0 end-0 mb-3 me-2 z-3 bg-secondary local-stream-container">
                    <div class="h-100 waiting-box bg-secondary d-flex">
                        <div class="m-auto text-center">
                            <small class="fw-light text-light">Start Your Camera <br> To Connect Call</small>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once "../../includes/callFooter.php" ?>
        </div>
    </div>



    <!-- POPUPS -->
    <?php require_once "../../includes/popups.php" ?>

    <!-- SCRIPTS -->
    <script src="../../js/wsconnection.js"></script>
    <script src="../../js/functions.js"></script>
    <script src="../../js/popup.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js'></script>

    <?php

    echo "<script>
            const userId = '$localUser',
                  remoteUser = '$remoteUser';
         </script>";

    $scripts = ['config', 'handlers', 'recorder', 'rtc', 'functions', 'videoCall'];

    foreach ($scripts as $script) {
        echo "<script src='{$baseurl}js/call/{$script}.js'></script>";
    }

    ?>

</body>

</html>