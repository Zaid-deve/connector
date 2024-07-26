<div class="header fixed-top w-100">
    <div class="container-fluid position-relative">
        <div class="nav d-flex align-items-center">
            <div class="app-logo me-auto">
                <a href="<?php echo $baseurl ?>" class="btn">
                    <img src="<?php echo $baseurl . 'images/app-logo-3.png' ?>" alt="#" class="app-logo-src">
                </a>
            </div>

            <?php if (!isset($hideRightHeader)) { ?>
                <div class="header-right d-flex align-items-center ms-sm-0 ms-auto">
                    <div class="position-relative">
                        <button class="btn btn-toggle-search header-btn" title="search friends">
                            <i class="ri-search-2-line"></i>
                        </button>
                        <button class="btn btn-toggle-requests header-btn position-relative" title="view requests">
                            <i class="ri-group-line"></i>
                            <div class="position-absolute bottom-25 start-50 translate-middle-x badge rounded-pill mt-1 fw-lighter pending-req-badge d-none">
                                + 0
                                <small class="visually-hidden">Pending Requests</small>
                            </div>
                        </button>

                        <?php

                        if (!isset($hideProfile)) {
                            $header_user = new User();
                            $huname = $header_profile = "";
                            if ($user->isUserLogedIn()) {
                                $header_uri = "profile.php";
                                $p = $header_user->getUser($conn, $header_user->getUserId(), ['user_profile', 'user_name']);
                                $huname = base64_decode($p['user_name']);
                            } else $header_uri = "signin.php";

                            $header_profile = $user::getProfileUri($p['user_profile'] ?? 'false');
                            echo "<a href='{$baseurl}/user/$header_uri' tilte='$huname' class='btn rounded-circle bg-light border-0 p-0 ms-1'><img src='$header_profile' height='38' width='38' class='rounded-circle img-cover bg-light'></a>";
                        }

                        ?>
                    </div>
                </div>

                <div class="position-absolute requests-menu-container">
                    <div class="bg-white rounded-3 requests-menu">
                        <div class="px-3 py-2 d-flex align-items-center justify-content-between bg-light">
                            <div class="text-muted h5 fw-normal m-0">+ New Requests.</div>
                            <button class="btn btn-toggle-sendreq header-btn rounded-circle" title="add new friend"><i class="ri-user-add-line"></i></button>
                        </div>
                        <hr class="m-0">
                        <div class="requests-output">
                            <div class="text-center text-muted py-4">fething requests...</div>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>
    </div>
</div>