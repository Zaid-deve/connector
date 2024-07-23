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
<link rel="stylesheet" href="../../styles/alert.css">
<link rel="stylesheet" href="../../styles/popups.css">
</head>

<body>

    <!-- BODY -->
    <?php require_once "../../includes/alert.php" ?>
    <div class="container-fluid vh-100 p-0 ui-box">
        <div class="d-flex flex-column justify-content-between h-100">
            <div class="d-flex align-items-center bg-white p-2 top-controls">
                <h1 class="me-auto">Connector</h1>
                <button class="btn rounded-circle" disabled><i class="ri-camera-switch-line fw-lighter"></i></button>
                <button class="btn rounded-circle" id="muteCallBtn"><i class="ri-mic-off-line fw-lighter"></i></button>
            </div>

            <div class="m-auto text-center">
                <div class="container-icon bg-dark rounded-circle m-auto">
                    <i class="ri-phone-line text-light"></i>
                </div>
                <h3 class="mt-3 mb-0">Audio Call</h3>
                <small class="text-muted">Your Call Is End-To-End Encrypted</small>
            </div>

            <div class="bg-white rounded-md-3 p-4">
                <div class="d-flex align-items-center justify-content-between flex-md-row flex-column gap-4">
                    <div class="d-flex align-items-center gap-3 remote-peer-card w-100">
                        <img src="<?php echo $remoteUserProfile ?>" alt="#" class="remote-peer-img img-cover rounded-circle">
                        <div class="remote-peer-info">
                            <div><?php echo base64_decode($remoteUser) ?></div>
                            <small class="call-status">waiting...</small>
                        </div>
                        <div class="ms-md-5 ms-auto call-time">0:00</div>
                        <audio src="#" autoplay id="remoteAudio"></audio>
                    </div>
                    <div>
                        <button class="btn btn-decline-call d-flex align-items-center gap-3 rounded-5 py-2 px-4">
                            <div class="decline-icon">
                                <i class="ri-phone-line text-light"></i>
                            </div>
                            <span class="fw-bold text-light">Decline</span>
                        </button>
                    </div>
                </div>
            </div>
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

    $scripts = ['config', 'handlers', 'recorder', 'rtc', 'functions', 'voiceCall'];

    foreach ($scripts as $script) {
        echo "<script src='{$baseurl}js/call/{$script}.js'></script>";
    }

    ?>

</body>

</html>