<div class="bg-white rounded-md-3 p-4" id="callFooter">
    <div class="d-flex align-items-center justify-content-between flex-md-row flex-column gap-4">
        <div class="d-flex align-items-center gap-3 remote-peer-card w-100">
            <img src="<?php echo $remoteUserProfile ?>" alt="#" class="remote-peer-img img-cover rounded-circle">
            <div class="remote-peer-info">
                <div><?php echo base64_decode($remoteUser) ?></div>
                <small class="call-status">waiting...</small>
            </div>
            <div class="ms-md-5 ms-auto call-time">0:00</div>
            <audio src="#" autoplay id="remoteAudio"></audio>
        </div>
        <div>
            <button class="btn btn-decline-call d-flex align-items-center gap-3 rounded-5 py-2 px-4">
                <div class="decline-icon">
                    <i class="ri-phone-line text-light"></i>
                </div>
                <span class="fw-bold text-light">Decline</span>
            </button>
        </div>
    </div>
</div>