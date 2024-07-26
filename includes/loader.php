<div class="fixed-top vh-100 vw-100 bg-white page-loader" style="z-index: 1100;">
    <div class="container-fluid h-100 d-flex">
        <div class="m-auto">
            <div class="d-flex flex-column gap-3 align-items-center">
                <img src="<?php echo $baseurl ?>images/app-logo-3.png" alt="__Connector__" style="max-width: 280px;">
                <svg width="30px" height="30px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" fill="#000">
                    <circle cx="50" cy="50" r="44" stroke-width="8" stroke="#000" stroke-linecap="round" fill="none" stroke-dasharray="138.23 138.23" stroke-dashoffset="69.115">
                        <animateTransform attributeName="transform" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                    </circle>
                </svg>

            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener("DOMContentLoaded", function() {
        document.querySelector('.page-loader').classList.add('d-none');
    })
</script>