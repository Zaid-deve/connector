<div class="header fixed-top w-100">
    <div class="container-fluid position-relative">
        <div class="nav d-flex align-items-center">
            <div class="brand me-auto">
                <div class="brand-text mb-3 fw-bolder">
                    connector
                </div>
            </div>

            <div class="header-right d-flex align-items-center ms-sm-0 ms-auto">
                <div class="position-relative">
                    <button class="btn btn-toggle-requests header-btn">
                        <i class="ri-group-line"></i>
                    </button>
                </div>

                <?php

                // get current user
                if (isset($_SESSION['user_id'])) {
                    $uid = $_SESSION['user_id'];
                    if (isset($conn)) {
                        $stmt = $conn->prepare("SELECT user_name, user_profile FROM users WHERE user_id = ?");
                        $stmt->execute([$uid]);
                        if ($stmt && $stmt->rowCount()) {
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            $user_name = base64_decode($user['user_name']);
                            $user_profile = $user['user_profile'];
                            if (!$user_profile || !file_exists($root . $user_profile)) {
                                $user_profile = $baseurl . 'images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif';
                            } else $user_profile = $baseurl . $user_profile;

                            echo "<a href='app/user/account.php' title='$user_name' class='btn btn-profile-link rounded-circle ms-2'>
                                      <img src='$user_profile' alt='#' class='rounded-circle bg-secondary img-cover'>
                                  </a>";
                        }
                    }
                }

                ?>


            </div>

            <div class="position-absolute requests-menu-container">
                <div class="bg-white rounded-3 requests-menu">
                    <div class="px-3 py-2 d-flex align-items-center justify-content-between">
                        <div class="text-muted">Friend Requests</div>
                        <button class="btn btn-toggle-sendreq header-btn rounded-circle">+</button>
                    </div>
                    <hr class="m-0">
                    <div class="requests-output">
                        <div class="text-center text-muted py-3">fething requests...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>