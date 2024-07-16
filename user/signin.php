<?php

require_once "../php/config.php";
require_once "user.php";
$user = new User();
if ($user->isUserLogedIn()) {
    header("Location:app/chat.php");
    die();
}

// page
$pagename = 'Sign In';
require_once "../includes/head.php";

?>

<link rel="stylesheet" href="../styles/signin.css">
</head>

<body>

    <!-- BODY -->
    <?php require_once "../includes/header.php" ?>
    <?php require_once "../includes/alert.php" ?>
    <main>
        <div class="container vh-100 d-flex">
            <div class="m-auto form-container">
                <form action="#" id="signinform" autocomplete="off" autocapitalize="off">
                    <h1 class="text-secondary mb-0">Sign In</h1>
                    <small class="text-muted">We Keep Your Data Private And Encrypted</small>
                    <hr>
                    <div class="mt-4">
                        <div class="field field-email">
                            <label for="__email">Email Address</label>
                            <input type="text" class="form-control" id="__email" placeholder="example@mail.com">
                            <div class="err text-danger"></div>
                        </div>
                        <div class="field field-pass mt-3 d-none">
                            <label for="__pass">Account Password</label>
                            <input type="password" class="form-control" id="__pass" placeholder="Enter password">
                            <div class="err text-danger"></div>
                        </div>
                        <button class="btn w-100 py-2 mt-3 rounded-5" type="submit" id="submit" disabled>
                            <span class="text-light fw-bold btn-text">Continue</span> 
                            <i class="ri-arrow-right-line ms-1 text-light"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>


    <!-- scripts -->
    <script src="../js/functions.js"></script>
    <script src="../js/signin.js"></script>
</body>

</html>