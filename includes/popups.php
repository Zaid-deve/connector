<div class="fixed-top popup-container d-none">
    <div class="container-fluid vh-100 vw-100 popup-outer">
        <!-- set username and common name profile -->
        <div class="popup popup-set-profile h-100 w-100">
            <div class="popup-card m-auto">
                <div class="popup-header popup-set-profile-header">
                    <h3>Set Your Profile</h3>
                    <small>Username and name is visible to others, useful to find you.</small>
                </div>
                <div class="popup-body popup-set-profile-body">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="d-flex align-items-center flex-column">
                            <label for="__user__profile__inp">
                                <img src="../images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif" alt="user profile" class="rounded-circle img-cover __preview__imgsrc" height="60" width="60">
                            </label>
                            <small class="text-danger fw-light text-center mt-2 __user__profile__err"></small>
                            <input type="file" id="__user__profile__inp" accept="image/*" hidden>
                        </div>

                        <div class="w-100">
                            <div class="field d-flex align-items-center">
                                <i class="ri-search-2-line"></i>
                                <input type="text" id="__username" class="form-control border-0" placeholder="Enter Your Username">
                            </div>
                            <div class="mt-1">
                                <small class="field-text fw-light __username__err"></small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="field d-flex align-items-center">
                            <i class="ri-user-line"></i>
                            <input type="text" id="__name" class="form-control border-0" placeholder="Enter Your Name">
                        </div>
                        <div class="mt-1">
                            <small class="field-text __name__err"></small>
                        </div>
                    </div>

                    <button class="btn btn-dark rounded-2 mt-3 py-2 w-100" id="__btn_create_profile" disabled>Submit</button>
                </div>
            </div>
        </div>

        <!-- popup call ended -->
        <div class="popup popup-call-ended h-100 w-100">
            <div class="popup-card m-auto">

                <div class="popup-header">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <a class="btn btn-white btn-back rounded-circle p-0" href="../chat.php">
                            <i class="ri-arrow-left-line "></i>
                        </a>
                        <h3 class="flex-shrink-0 flex-grow-1">Call ended</h3>
                        <a class="btn btn-primary rounded-5 px-3 py-2 flex-shrink-0 flex-grow-1 d-none fw-bold" id="saveCallBtn">
                            <i class="ri-download-line fw-bolder text-light"></i> <span class="ms-2 text-light">Save Call</span>
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
        </div>

        <!-- accept request -->
        <div class="popup popup-dismisable popup-make-call h-100 w-100">
            <div class="popup-card m-auto">
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
                    <div class="popup-make-call-btns d-flex flex-column flex-sm-row gap-2 mt-3">
                        <button class="btn rounded-5 py-2 px-3 flex-grow-1" id="__btn__voice__call">
                            <i class="ri-phone-line fw-lighter h5"></i>
                            <span class="fw-bold ms-2">Audio Call</span>
                        </button>
                        <button class="btn rounded-5 py-2 px-3 flex-grow-1" id="__btn__video__call">
                            <i class="ri-vidicon-line fw-lighter h5 text-light"></i>
                            <span class="fw-bold ms-2 text-light">Video Call</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- popup accept req -->
        <div class="popup popup-inc-call h-100 w-100">
            <div class="popup-card m-auto">
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


        <!-- connection error -->
        <div class="popup popup-dismisable popup-connect-error h-100 w-100">
            <div class="popup-card m-auto">
                <div class="popup-header popup-connect-error">
                    <h3>Connection Error</h3>
                    <small>something went wrong !</small>
                </div>
                <div class="popup-body popup-connect-error-body">
                    <h6>Troubleshoot Error: </h6>
                    <ul>
                        <li>check you internet connection</li>
                        <li>reload the page and try again</li>
                        <li>report this problem</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- accept request -->
        <div class="popup popup-dismisable popup-accept-req h-100 w-100">
            <div class="popup-card m-auto">
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
        </div>

        <!-- popup add friend -->
        <div class="popup popup-dismisable popup-add-friend h-100 w-100">
            <div class="popup-card m-auto">
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
        </div>
    </div>
</div>