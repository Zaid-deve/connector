<?php

session_start();
require_once "../php/config.php";
require_once "../php/functions.php";

$uid = getUserId();
if (!$uid) {
    // user is not loged in
    header("Location:../user/signin.php");
    die();
}

// get user public id
require_once "../db/conn.php";
$stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
$stmt->execute([$uid]);

// fetch
$userId = "";
if ($stmt && $stmt->rowCount()) {
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data['user_name']) {
        $userId = $data['user_name'];
        $userIdDec = base64_decode($userId);
    }
}

// includes head
$pagename = "Home";
require_once $root . "includes/head.php";

?>

<link rel="stylesheet" href="<?php echo $baseurl ?>styles/chat.css">
<link rel="stylesheet" href="<?php echo $baseurl ?>styles/alert.css">
<link rel="stylesheet" href="<?php echo $baseurl ?>styles/popups.css">
</head>

<body>

    <!-- BODY -->
    <?php include "{$root}/includes/header.php"; ?>
    <?php include "{$root}/includes/alert.php"; ?>

    <!-- 
    =========== MAIN CONTENT ===========
    -->

    <main class="chat-container">
        <div class="container-fluid h-100 p-0">
            <div class="row m-0 g-0 h-100">
                <div class="col-12 col-md-5 col-lg-4 h-100 friends-list-container">
                    <div class="friends-list-outer">
                        <?php

                        $NO_RETURN = TRUE;
                        require_once "../php/fetchFriends.php";
                        if ($response['Users']) {
                            echo "<ul class='list-group friends-list'>";
                            foreach ($response['Users'] as $f) {
                                echo "<li class='list-group-item border-0 rounded-0 friend-list-item py-3' onclick=\"previewCallOptions('{$f['username']}', '{$f['name']}', '{$f['profile']}')\">
                                          <div class='d-flex align-items-center gap-2'>
                                              <img src='../{$f['profile']}' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                              <div class='friend-info flex-shrink-0'>
                                                  <div class='fw-bold'>@{$f['username']}</div>
                                                  <small class='text-muted fw-light'>{$f['name']}</small>
                                              </div>
                                              <div class='ms-auto'>
                                                  <small class='text-muted'>offline</small>
                                              </div>
                                          </div>
                                      </li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<div class='text-success text-center fw-light py-5 px-3'>
                                     <h1>🤔</h1>
                                     add friends to quikly <br> make a video/audio call with them.
                                  </div>";
                        }

                        ?>

                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-8 d-none d-md-block">
                    <div class="chat-intro pt-5 px-3">
                        <div class="text-center text-success">
                            <img src="../images/happy-people-are-talking-phone-set-characters-vector-illustration_324395-532.webp" alt="#" class="img-contain d-block m-auto hero-img" height="245">
                            <h1 class="mt-4">Welcome to the <span class="fw-bold text-primary f-logo">connector</span></h1>
                            <p class=" mt-1 fw-light">This is a chat application that allows you to chat with other</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 
        
        =================================
               POPUP WINDOWS
        ================================

    -->


    <?php $dclass = $userId ? 'd-none' : ''; ?>
    <div class="popup-container fixed-top vh-100 vw-100 <?php echo $dclass; ?>">
        <div class="container-fluid h-100 d-flex">
            <?php if (!$userId) { ?>
                <!-- set username and common name profile -->
                <div class="popup popup-set-profile">
                    <div class="popup-header popup-set-profile-header">
                        <h3>Set Your Profile</h3>
                        <small>Username and name is visible to others, useful to find you.</small>
                    </div>
                    <div class="popup-body popup-set-profile-body">
                        <div class="d-flex align-items-center flex-column">
                            <label for="__user__profile__inp">
                                <img src="../images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif" alt="user profile" class="rounded-circle img-cover __preview__imgsrc" height="70" width="70">
                            </label>
                            <small class="text-danger text-center mt-2 __user__profile__err"></small>
                            <input type="file" id="__user__profile__inp" accept="image/*" hidden>
                        </div>

                        <div class="mt-3">
                            <div class="field d-flex align-items-center">
                                <i class="ri-speak-line"></i>
                                <input type="text" id="__username" class="form-control border-0" placeholder="Enter Your Username">
                            </div>
                            <div class="mt-1">
                                <small class="field-text __username__err">letters, numbers, _ (underscore) and . (fullstop) allowed</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="field d-flex align-items-center">
                                <i class="ri-group-2-line"></i>
                                <input type="text" id="__name" class="form-control border-0" placeholder="Enter Your Name">
                            </div>
                            <div class="mt-1">
                                <small class="field-text __name__err">letters, numbers, _ (underscore) and Whitespace is allowed </small>
                            </div>
                        </div>

                        <button class="btn btn-dark rounded-2 mt-3 py-2 w-100" id="__btn_create_profile" disabled>Submit</button>
                    </div>
                </div>

            <?php } ?>


            <!-- add a new friend -->
            <div class="popup popup-dismisable popup-add-friend d-none">
                <div class="popup-header popup-add-friend-header">
                    <h3>Send Request</h3>
                    <small>you will be added the movement your friend accepts your request</small>
                </div>
                <div class="popup-body popup-add-friend-body">
                    <div>
                        <div class="field d-flex align-items-center">
                            <i class="ri-user-line"></i>
                            <input type="text" id="__friend__id__inp" class="form-control border-0" placeholder="Enter Friend Username">
                        </div>
                        <div class="mt-1">
                            <small class="field-text text-danger __friend__id__err"></small>
                        </div>
                    </div>

                    <button class="btn btn-dark rounded-2 mt-3 py-2 w-100" id="__btn__sendreq" disabled>Submit</button>
                </div>
            </div>

            <!-- accept request -->
            <div class="popup popop-dismisable popup-accept-req d-none">
                <div class="popup-header popup-accept-req-header">
                    <h3>Accept Request</h3>
                    <small>now this user will let you call even when you are offline</small>
                </div>
                <div class="popup-body popup-accept-req-body">
                    <div class="req-info-body"></div>
                    <div class="d-flex gap-2">
                        <button class="btn rounded-2 mt-3 py-2 w-50" id="__btn__reject__req">Reject</button>
                        <button class="btn btn-dark rounded-2 mt-3 py-2 w-50" id="__btn__accept__req">Accept</button>
                    </div>
                </div>
            </div>

            <!-- accept request -->
            <div class="popup popup-dismisable popup-make-call d-none">
                <div class="popup-header popup-make-call-header text-center">
                    <div class="make-call-icon popup-header-icon">
                        <i class="ri-phone-fill"></i>
                    </div>
                    <h3 class="mt-2">Make A Call</h3>
                </div>
                <div class="popup-body popup-make-call-body">
                    <div class='d-flex align-items-center gap-2'>
                        <img src='../images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img make-call-profile' height='46' width='46'>
                        <div class='friend-info flex-shrink-0'>
                            <div class='fw-bold remote-username'>unknown</div>
                            <small class='text-muted fw-light remote-name'></small>
                        </div>
                        <div class='ms-auto'>
                            <small class='text-muted remote-status'>offline</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary rounded-2 mt-3 py-2 w-50" id="__btn__voice__call"><i class="ri-phone-fill text-light"></i>&nbsp; Audio Call</button>
                        <button class="btn btn-dark rounded-2 mt-3 py-2 w-50" id="__btn__video__call"><i class="ri-video-chat-line text-light fw-lighter"></i>&nbsp; Video Call</button>
                    </div>
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
                            <small class='text-muted inc-time-left'>(40)</small>
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

    <!-- scripts -->
    <script src="../js/functions.js"></script>
    <script src="../js/modifyRequest.js"></script>
    <script src="../js/header.js"></script>
    <script src="../js/popop.js"></script>
    <?php

    if (!empty($userId)) {
        echo "<script>
                const userId = '$userId';
             </script>
             <script src='../js/wsconnection.js'></script>
             <script src='https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js'></script>
             <script src='../js/notifyCall.js'></script>
             <script src='../js/sendRequest.js'></script>";
    } else {
        echo "<script src='../js/createProfile.js'></script>";
    }

    ?>



</body>

</html>