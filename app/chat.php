<?php

require_once "../php/config.php";
require_once "../php/functions.php";
require_once "../db/conn.php";
require_once "../user/user.php";
$user = new User(true);
$uid = $user->getUserId();

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
    <?php
    include "../includes/loader.php";

    // get user public id
    $userId = $userName = $userProfile = '';
    if ($uid) {
        $data = $user->getUser($conn, $uid, ['user_name', 'user_profile']);
        if ($data) {
            $userId = $data['user_name'];
            $userName = base64_decode($userId);
            $userProfile = $user->getProfileUri($data['user_profile']);
        }
    }


    include "{$root}/includes/header.php";
    include "{$root}/includes/alert.php";


    ?>

    <!-- 
    =========== MAIN CONTENT ===========
    -->

    <main class="chat-container">
        <div class="container-fluid h-100 p-0">
            <div class="row m-0 g-0 h-100">
                <div class="col-12 col-md-5 col-lg-4 h-100 friends-list-container">
                    <div class="friends-list-outer position-relative">

                        <!-- context menu -->
                        <div class="chat-context-menu position-absolute d-none">
                            <div class="bg-white rounded-3 py-2">
                                <ul class="list-group rounded-0">
                                    <li class="list-group-item d-flex align-items-center gap-3 w-100 context-menu-delete-btn">
                                        <span class="chat-context-icon"><i class="ri-user-4-fill"></i></span>
                                        <span class="chat-context-text fw-normal">Remove Friend</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center gap-3 w-100 context-menu-block-btn">
                                        <span class="chat-context-icon"><i class="ri-user-forbid-fill"></i></span>
                                        <span class="chat-context-text fw-normal">Block Friend</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center gap-3 w-100 context-menu-star-btn">
                                        <span class="chat-context-icon"><i class="ri-star-fill"></i></span>
                                        <span class="chat-context-text fw-normal">Mark As Star</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class='p-3 pb-0'>
                            <?php require_once "../includes/search.php" ?>
                        </div>

                        <?php

                        $NO_RETURN = TRUE;
                        require_once "../php/fetchFriends.php";
                        echo "<div class='friends-list-box'>";
                        if ($response['Users']) {
                            echo "<ul class='list-group friends-list'>";
                            foreach ($response['Users'] as $f) {
                                $username = $f['username'];
                                $name = $f['name'];
                                $peer = base64_encode($username);
                                $profile = $f['profile'];;
                                $starFriendicon = '';
                                $isStar = $f['isStarFriend'];
                                $isBlocked = $f['isBlockedFriend'];
                                $blockStr = $isBlocked ? " text-danger'> <i class='ri-prohibited-2-line text-danger'></i> " : "'>";
                                if ($isStar) {
                                    $starFriendicon = "<div class='star-friend-icon'> <i class='ri-star-smile-fill'></i> </div>";
                                }

                                echo "<li class='list-group-item border-0 rounded-0 friend-list-item py-3' oncontextmenu='toggleContextMenu(event)' data-enc-username='$peer' data-username='$username' data-name='$name' data-profile='$profile' data-isfriend='1' data-isstar='$isStar' data-isblocked='$isBlocked' onclick='showRemoteCaller(event)'>
                                          <div class='d-flex align-items-center gap-2'>
                                              <img src='$profile' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                              <div class='friend-info flex-shrink-0'>
                                                  <div class='fw-bold friend-username $blockStr @$username</div>
                                                  <small class='text-muted fw-light'>$name</small>
                                              </div>
                                              <div class='ms-auto text-center list-item-right'>
                                                  $starFriendicon
                                                  <small class='text-muted mt-1'>offline</small>
                                              </div>
                                          </div>
                                      </li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<div class='py-5 px-3 d-flex justify-content-center flex-column gap-4'>
                                     <img src='../images/no-users.png' class='img-contain d-block mx-auto' style='max-height:140px;'>
                                     <p class='text-center text-secondary'>Add friends and make <br> video and audio calls seamlesly.</p>
                                  </div>";
                        }
                        echo "</div>";

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


    <?php require_once "../includes/popups.php"; ?>

    <!-- scripts -->
    <script src="../js/functions.js"></script>
    <script src="../js/header.js"></script>
    <script src="../js/popup.js"></script>
    <script src="../js/contextFunctions.js"></script>
    <script src="../js/contextMenu.js"></script>
    <script src="../js/search.js"></script>
    <?php

    if (!empty($userId)) {
        echo "<script>
                const userId = '$userId',
                      isProfileAdded = userId;
             </script>
             <script src='../js/createProfile.js'></script>
             <script src='../js/wsconnection.js'></script>
             <script src='https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js'></script>
             <script src='../js/notifyCall.js'></script>
             <script src='../js/sendRequest.js'></script>
             <script src='../js/modifyRequest.js'></script>";
    } else {
        echo "<script src='../js/createProfile.js'></script>";
    }

    ?>



</body>

</html>