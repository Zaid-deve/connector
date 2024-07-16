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
    <div class="fixed-top vh-100 vw-100 popup-container bg-light d-none">
        <div class="container-fluid h-100 d-flex">
            <div class="popup popup-call-ended m-auto d-none">
                <div class="popup-header">
                    <div class="d-flex align-items-center gap-3">
                        <a class="btn btn-back rounded-circle p-0" href="../chat.php">
                            <i class="ri-arrow-left-line fw-lighter"></i>
                        </a>
                        <h3 class="fw-normal">Call ended</h3>
                        <a class="btn btn-primary fw-bolder rounded-5 p-0 ms-auto d-flex align-items-center gap-2 px-3 py-2 d-none" id="saveCallBtn">
                            <i class="ri-download-line fw-lighter text-light"></i> save this call
                        </a>
                    </div>
                </div>
                <div class="alert alert-warning rounded-0 w-100 call-end-err d-none"></div>
                <div class="popup-body">
                    <div class="d-flex align-items-center gap-3 remote-peer-card w-100">
                        <img src="<?php echo $remoteUserProfile ?>" alt="#" class="remote-peer-img img-cover rounded-circle">
                        <div class="remote-peer-info">
                            <div><?php echo base64_decode($remoteUser) ?></div>
                            <small><?php echo base64_decode($remoteUserCname) ?></small>
                        </div>
                        <div class="ms-auto call-time">0:00</div>
                        <audio src="#" autoplay id="remoteAudio"></audio>
                    </div>

                    <a href="<?php $baseurl . "app/call/voice.php?userId=$remoteUser" ?>" class="btn btn-call-again btn-dark fw-bold py-2 d-block w-100 rounded-4 mt-4">Call Again</a>
                </div>
            </div>

            <!-- accept request -->
            <div class="popup popup-inc-call d-none">
                <div class="popup-header popup-inc-call-header text-center">
                    <div class="inc-icon popup-header-icon">
                        <i class="ri-phone-fill"></i>
                    </div>
                    <h3 class="mt-2">Incomming Call</h3>
                    <small class="inc-call-msg"></small>
                </div>
                <div class="popup-body popup-inc-body">
                    <div class='d-flex align-items-center gap-2'>
                        <img src='../images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img inc-profile' height='46' width='46'>
                        <div class='friend-info flex-shrink-0'>
                            <div class='fw-bold inc-username'></div>
                            <small class='text-muted fw-light inc-name'></small>
                        </div>
                        <div class='ms-auto'>
                            <small class='text-muted inc-time-left'>(40s)</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn rounded-2 mt-3 py-2 w-50" id="__btn__reject__call">Reject</button>
                        <button class="btn btn-dark rounded-2 mt-3 py-2 w-50" id="__btn__accept__call">Answer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- SCRIPTS -->
    <?php

    echo "<script>
            const userId = '$localUser',
                  remoteUser = '$remoteUser';
         </script>";

    $scripts = ['wsconnection', 'functions', 'popup', 'recorder', 'rtcfunctions'];
    if (isset($_GET['type']) && $_GET['type'] == 'answer') {
        $scripts[] = 'answerVoiceCall';
    } else {
        $scripts[] = 'voiceCall';
    }

    foreach ($scripts as $script) {
        echo "<script src='{$baseurl}js/{$script}.js'></script>";
    }

    ?>

</body>

</html>