<?php

require_once "../php/functions.php";
session_start();
if (getUserId()) {
    header("Location:app/chat.php");
    die();
}

// page
$pagename = 'Sign In';
require_once "../php/config.php";
require_once $root . "includes/head.php";

?>

<link rel="stylesheet" href="<?php echo $baseurl ?>styles/signin.css">
</head>

<body>

    <!-- BODY -->
    <?php require_once "../includes/header.php" ?>
    <?php require_once "../includes/alert.php" ?>
    <main>
        <div class="container-fluid h-100 d-flex">
            <div class="m-auto">
                <form class="signin-form" onsubmit="return false">
                    <h1 class="m-0">Signin To Account</h1>
                    <p class="text-muted">connector ensures your privacy, your data is safe</p>
                    <div class="mt-4">
                        <div class="field-email">
                            <div class="field d-flex align-items-center">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="text" placeholder="Enter Your Email Address" name="email" id="email" class="form-control border-0" autofocus>
                            </div>
                            <div class="tnormal tdanger email-err"></div>
                        </div>
                        <div class="field-pass mt-2 d-none">
                            <div class="field d-flex align-items-center">
                                <i class="fa-solid fa-key"></i>
                                <input type="password" placeholder="Enter Your Password" name="pass" id="pass" class="form-control border-0">
                            </div>
                            <div class="tnormal tdanger pass-err"></div>
                        </div>
                        <button class="btn bdark rounded-5 px-4 py-2 mt-3 d-block mx-auto mt-4 border-0" disabled type="submit" id="submit">
                            <span class="tlight">CONTINUE</span>
                            <i class="fa-solid fa-spinner tlight"></i>
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