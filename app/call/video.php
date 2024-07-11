<?php

$pagename = 'Video Call';
require_once "../../php/config.php";
require_once "../../includes/head.php";

?>

<link rel="stylesheet" href="../../styles/call.css">
</head>

<body>

    <!-- BODY -->
    <?php include "{$root}/includes/alert.php"; ?>
    <main class="vh-100 vw-100 blight p-0">
        <div class="container-fluid p-0 h-100 position-relative">
            <div class="position-absolute start-50 bg-white rounded-5 p-4 float-win float-info-win">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../images/profile.png" alt="#" class="user-img rounded-circle blight" height="50px">
                    <div class="user-info">
                        <div class="tbold">user000</div>
                        <small class="tnormal">ringing...</small>
                    </div>
                    <div class="call-time ms-auto">0:00</div>
                </div>

                <button class="btn btn-decline-call bdanger rounded-circle d-block mx-auto mt-3">
                    <i class="fa-solid fa-phone"></i>
                </button>
            </div>
            <div class="position-absolute top-0 start-0" style="z-index: 4;">
                <div class="d-flex align-items-center cbtns gap-4 p-3">
                    <button class="btn bg-white rounded-circle"><i class="fa-solid fa-video"></i></button>
                </div>
            </div>
            <div class="video-box h-100 w-100 position-relative">
                <div class="row m-0 g-0 h-100">
                    <div class="col-md-8 h-100">
                        <div class="remote-peer h-100 w-100"></div>
                    </div>
                </div>
                <div class="position-absolute top-0 end-0">
                    <div class="local-peer"></div>
                </div>
            </div>
        </div>

        <div class="fixed-top h-100 w-100 bg-light d-none">
            <div class="container-fluid h-100 d-flex">
                <div class="m-auto bg-white rounded-5 p-4 float-win">
                    <h6>Call Ended</h6>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <img src="../../images/profile.png" alt="#" class="user-img rounded-circle blight" height="50px">
                        <div class="user-info">
                            <div class="tbold">user000</div>
                            <small class="tnormal">offline</small>
                        </div>
                        <div class="call-time ms-auto">0:00</div>
                    </div>
                    <div class="d-flex mt-3 gap-3">
                        <button class="btn blight rounded-5 px-4 py-2 w-50"><i class="fa-solid fa-save"></i>&nbsp; SAVE CALL</button>
                        <button class="btn bdark tlight rounded-5 px-4 py-2 w-50"><i class="fa-solid tlight fa-repeat"></i>&nbsp; CALL AGAIN</button>
                    </div>
                </div>
            </div>
        </div>
    </main>


</body>

</html>