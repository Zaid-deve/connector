<?php

require_once "php/config.php";
require_once "user/user.php";
$user = new User();

if ($user->isUserLogedIn()) {
    $uid = $user->getUserId();
}


require_once "./includes/head.php";
?>
<link rel="stylesheet" href="styles/config.css">
<link rel="stylesheet" href="styles/header.css">
<link rel="stylesheet" href="styles/home.css">
</head>

<body>

    <?php
    
    $hideProfile = true;
    include "includes/header.php";
    
    ?>

    <main>
        <div class="container vh-100 d-flex">
            <div class="d-flex align-items-center justify-content-between gap-3 m-auto w-100">
                <div class="hleft d-none d-md-block"></div>
                <div class="htext text-center text-md-start">
                    <h1 class="fw-bolder">Welcome to <strong>connector</strong></h1>
                    <p class="text-muted mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. <br> Aperiam autem illo quasi magni saepe nobis dolorem minima ullam a enim?</p>
                    <?php if(!$user->isUserLogedIn()){
                        echo "<a href='user/signin.php' class='btn fw-bold rounded-5 px-5 py-2 text-light'>Login</a>";
                    } else {
                        echo "<a href='app/chat.php' class='btn fw-bold rounded-5 px-5 py-2 text-light'>Start A Call</a>";
                    } ?>
                </div>
            </div>
        </div>
    </main>

</body>

</html>