<?php

require_once "../php/config.php";
require_once "../db/conn.php";
require_once "user.php";
$user = new User();

if (!$user->isUserLogedIn()) {
    header("Location:signin.php");
    die();
}

$uid = $user->getUserId();
$data = $user->getUser($conn, $uid, ['user_email', 'user_profile', 'user_cname', 'user_name']);
$email = base64_decode($data['user_email']);
$profile = User::getProfileUri($data['user_profile']);
$userId = $data['user_name'];
$username = base64_decode($userId);
$name = base64_decode($data['user_cname']);

if (!$userId) {
    header("Location:?opt=edit");
    die();
}

// fetch pending requests and
$sql = " SELECT 
        (SELECT COUNT(*) 
         FROM user_requests ur 
         WHERE ur.recipient_user_id = :current_user 
        ) as newRequests,
        (SELECT COUNT(*) 
         FROM user_friends uf 
         WHERE (uf.sender_user_id = :current_user OR uf.recipient_user_id = :current_user) 
            AND uf.is_blocked = :current_user
        ) as blockedFriends,
        (SELECT COUNT(*) 
         FROM user_friends uf 
         WHERE (uf.sender_user_id = :current_user OR uf.recipient_user_id = :current_user) 
            AND uf.is_star = 1
        ) as starFriends";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':current_user' => $uid
]);

$newRequest = $blockedFriends = $starFriends = null;

if ($stmt) {
    if ($stmt->rowCount()) {
        $d = $stmt->fetch(PDO::FETCH_ASSOC);
        $newRequest = $d['newRequests'];
        $blockedFriends = $d['blockedFriends'];
        $starFriends = $d['starFriends'];
    }
}

require_once "../includes/head.php";
?>
<link rel="stylesheet" href="../styles/config.css">
<link rel="stylesheet" href="../styles/header.css">
<link rel="stylesheet" href="../styles/profile.css">
<link rel="stylesheet" href="../styles/form.css">
<link rel="stylesheet" href="../styles/popups.css">
</head>

<body>

    <?php

    include "../includes/loader.php";
    require_once "../includes/header.php";

    ?>

    <main>
        <div class="container-fluid p-0">
            <div class="row vh-100 main-row m-0 g-0">
                <?php if (!isset($_GET['opt'])) { ?>
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="d-flex align-items-center gap-3 p-3">
                            <img src="<?php echo $profile ?>" alt="profile image" class="img-cover profile-img bg-light rounded-circle" height="46" width="46">
                            <div class="profile-info">
                                <div class="profile-username"><?php echo $username ?></div>
                                <small class="profile-name"><?php echo empty($name) ? 'no name added' : $name ?></small>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush profile-options">
                            <li class="list-group-item d-flex gap-3 align-items-center py-3 active" data-opt='edit'>
                                <i class="ri-pencil-line"></i> <span>Edit Profile</span>
                            </li>
                            <li class="list-group-item d-flex gap-3 align-items-center py-3" data-opt='logout'>
                                <i class="ri-shut-down-line"></i> <span>Logout</span>
                            </li>
                            <li class="list-group-item d-flex gap-3 align-items-center py-3" data-opt='requests'>
                                <i class="ri-user-add-fill"></i> <span>New Requests</span>
                                <?php if ($newRequest) {
                                    echo "<span class='badge bg-primary rounded-pill ms-auto'>$newRequest</span>";
                                } ?>
                            </li>
                            <li class="list-group-item d-flex gap-3 align-items-center py-3 " data-opt='star'>
                                <i class="ri-star-line"></i> <span>Star Friends</span>
                                <?php if ($starFriends) {
                                    echo "<span class='badge bg-primary rounded-pill ms-auto'>$starFriends</span>";
                                } ?>
                            </li>
                            <li class="list-group-item d-flex gap-3 align-items-center py-3 " data-opt='blockes'>
                                <i class="ri-user-forbid-line"></i> <span>Blocked Users</span>
                                <?php if ($blockedFriends) {
                                    echo "<span class='badge bg-primary rounded-pill ms-auto'>$blockedFriends</span>";
                                } ?>
                            </li>
                            <li class="list-group-item d-flex gap-3 align-items-center py-3 " data-opt='delete'>
                                <i class="ri-error-warning-line text-danger"></i> <span class="text-danger">Delete My Account</span>
                            </li>
                        </ul>
                    </div>

                <?php }

                $opt = $_GET['opt'] ?? null;
                if ($opt) {
                    echo "<div class='col h-100'>";
                    if (file_exists("{$root}user/$opt.php")) {
                        require_once "{$root}user/$opt.php";
                    } else {
                        require_once "{$root}user/edit.php";
                    }
                    echo "</div>";
                }

                ?>

            </div>
        </div>
    </main>

    <?php require_once "../includes/popups.php"; ?>

    <!-- scripts -->
    <script src="../js/functions.js"></script>
    <script src="../js/header.js"></script>
    <script src="../js/popup.js"></script>
    <script src="../js/contextFunctions.js"></script>
    <script src='../js/modifyRequest.js'></script>
    <script src="../js/user/profile.js"></script>
    <?php
    if (isset($opt)) {
        echo "<script src='{$baseurl}js/user/$opt.js'></script>";
    } else echo "<script src='{$baseurl}js/user/edit.js'></script>";
    echo "<script>
                const userId = '$userId',
                      isProfileAdded = userId;
             </script>";
    ?>
    <script src='../js/wsconnection.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js'></script>
    <script src='../js/notifyCall.js'></script>
    <script src='../js/sendRequest.js'></script>


</body>

</html>