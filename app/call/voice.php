<?php


// requires
require_once '../../php/config.php';
require_once '../../php/functions.php';
require_once '../../db/conn.php';

// user
session_start();
$uid = getUserId();
if (!$uid) {
    header('Location: ../chat.php');
    die();
}

// get target user id
$target_user = $_GET['userId'];
if (!$target_user) {
    header("Location: ../chat.php");
    die();
}

if (!isBase64($target_user)) {
    $target_user = base64_encode($target_user);
}

// query
$stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
$stmt->execute([$uid]);
if (!$stmt || !$stmt->rowCount()) {
    die("Something Went Wrong !");
}

// get caller info
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$callerId = $userData['user_name'];


// reciever indo
$stmt2 = $conn->prepare("SELECT user_name, user_cname, user_profile FROM users WHERE user_name= ?");
$stmt2->execute([$target_user]);
if (!$stmt2 || !$stmt2->rowCount()) {
    die("Remote user not found !");
}

// get remote user info
$remoteUserData = $stmt2->fetch(PDO::FETCH_ASSOC);
$remoteUserId = $remoteUserData['user_name'];
$remoteUserName = $remoteUserData['user_cname'];
$remoteUserProfile = $remoteUserData['user_profile'];

if (!$remoteUserProfile || !file_exists($root . $remoteUserProfile)) {
    $remoteUserProfile = 'images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif';
}

$pagename = 'Audio Call';
require_once "../../includes/head.php";
?>

<link rel="stylesheet" href="../../styles/call.css">
<link rel="stylesheet" href="../../styles/alert.css">
</head>

<body>

    <!-- BODY -->
    <?php include "{$root}/includes/alert.php"; ?>
    <main class="vh-100 vw-100 blight p-0">
        <div class="container-fluid h-100 position-relative">
            <div class="position-absolute start-50 bg-white rounded-5 p-4 float-win float-info-win">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../<?php echo $remoteUserProfile ?>" alt="#" class="user-img rec-img rounded-circle blight" height="50px">
                    <div class="user-info">
                        <div class="tbold rec-id"><?php echo base64_decode($remoteUserId) ?></div>
                        <small class="tnormal call-status">waiting...</small>
                    </div>
                    <div class="call-time ms-auto">0:00</div>
                </div>

                <button class="btn btn-decline-call bdanger rounded-circle d-block mx-auto mt-3">
                    <i class="fa-solid fa-phone"></i>
                </button>
            </div>
            <div class="position-absolute top-0 start-0">
                <div class="d-flex align-items-center cbtns gap-4 p-3">
                    <button class="btn bg-white rounded-circle"><i class="fa-solid fa-video"></i></button>
                </div>
            </div>
        </div>

        <div class="fixed-top h-100 w-100 bg-light d-none call-end-win">
            <div class="container-fluid h-100 d-flex">
                <div class="m-auto bg-white rounded-4 float-win">
                    <div class="text-center">
                        <div class="banner">
                            <h5 class="text-white call-header">Call Ended</h5>
                            <small class="fw-light text-muted call-msg"></small>
                        </div>
                    </div>
                    <div class="px-3 mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <img src="../../<?php echo $remoteUserProfile ?>" alt="#" class="user-img rec-img rounded-circle blight" height="50px">
                            <div class="user-info">
                                <div class="tbold rec-id"><?php echo base64_decode($remoteUserId) ?></div>
                                <small class="tnormal rec-name"><?php echo base64_decode($remoteUserName) ?></small>
                            </div>
                            <div class="call-time ms-auto">0:00</div>
                        </div>
                        <div class="d-flex align-items-end flex-column mt-3 gap-2 pb-3">
                            <button class="btn rounded-5 p-0" id="savebtn"><i class="fa-solid fa-arrow-down"></i>&nbsp; Save This Call</button>
                            <button class='btn btn-outline-secondary rounded-5 px-4 py-2' onclick='location.reload()'><i class='fa-solid text-secondary fa-repeat'></i>&nbsp; CALL AGAIN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <audio autoplay id="remoteAudio"></audio>

    <?php

    // caller info
    echo "<script>
            const userId = '$callerId',
                  remoteUser = '$remoteUserId'
         </script>";
    ?>

    <!-- scripts -->
    <script src="../../js/functions.js"></script>
    <script src="../../js/wsconnection.js"></script>
    <script src="../../js/rtcfunctions.js"></script>

    <?php


    if (isset($_GET['type']) && $_GET['type'] === 'answer') {
        echo "<script src='../../js/answer.js'></script>";
    } else {
        echo "<script src='../../js/call.js'></script>";
    }

    ?>
    <script src="../../js/callfunctions.js"></script>


</body>

</html>