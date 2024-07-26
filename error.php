<?php

require_once "php/config.php";

require_once "{$root}includes/head.php";
?>
<style>
    .error-document {
        padding-top: var(--header-height);
    }
    .error-image {
        height: 220px;
    }

    .error-image img {
        height: 100%;
        width: 100%;
    }

    @media (max-width:560px) {
        .error-image {
            height: 140px;
        }
    }
</style>
</head>


<body class="bg-white">

    <?php
    $hideRightHeader = true;
    require_once "{$root}includes/header.php"
    ?>

    <div class="error-document container vh-100 d-flex">
        <div class="m-auto" style="max-width: 420px;width:100%">
            <div class="error-image">
                <img src="<?php echo $baseurl . 'images/error.jpg'; ?>" alt="😒" class="img-contain">
            </div>

            <h1 class="error-title mt-3">Oops! Page Not Found !</h1>
            <p class="text-secondary">the page you are looking does not exists or moved !</p>
            <div class="text-center mt-3">
                <a href="/connector" class="btn rounded-2 px-5 py-3 fw-bold text-light d-block" style="background: var(--color-primary);">Go to Homepage</a>
            </div>
        </div>
    </div>
</body>

</html>