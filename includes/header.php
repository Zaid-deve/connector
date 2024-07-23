<div class="header fixed-top w-100">
    <div class="container-fluid position-relative">
        <div class="nav d-flex align-items-center">
            <div class="brand me-auto">
                <!-- <div class="brand-text mb-3 fw-bolder">
                    connector
                </div> -->
                <a href="<?php echo $baseurl ?>" class="btn">
                    <img src="<?php echo $baseurl . 'images/app-logo-5.png' ?>" alt="#" id="appLogo">
                </a>
            </div>

            <div class="header-right d-flex align-items-center ms-sm-0 ms-auto">
                <div class="position-relative me-2">
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
                </div>

                <?php

                // get current user
                if (!isset($hideProfile) && $user->isUserLogedIn()) {
                    echo "<a href='app/user/account.php' title='$userName' class='btn btn-profile-link rounded-circle ms-2'>
                              <img src='$userProfile' alt='#' class='rounded-circle bg-secondary img-cover'>
                          </a>";
                }

                ?>

            </div>

            <div class="position-absolute requests-menu-container">
                <div class="bg-white rounded-3 requests-menu">
                    <div class="px-3 py-2 d-flex align-items-center justify-content-between bg-light">
                        <h5 class="text-secondary">Friend <br> Requests</h5>
                        <button class="btn btn-toggle-sendreq header-btn rounded-circle"><i class="ri-user-add-line"></i></button>
                    </div>
                    <hr class="m-0">
                    <div class="requests-output">
                        <div class="text-center text-muted py-4">fething requests...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>