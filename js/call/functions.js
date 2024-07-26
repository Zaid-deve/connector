function startTimeout(init) {
    let canHangup = !['connecting', 'reconnecting', 'connected'].includes(callStatus);

    const curr = Date.now(),
        gvn = init + 40000,
        diff = Math.floor(gvn - curr);

    if (diff > 0) {
        setTimeout(() => {
            startTimeout(init);
        }, 1000);
    } else {
        if (canHangup) {
            sendHangupReq();
            hangupCall()
        }
    }
}

function showCallTime(init) {
    if (peer && callStatus != 'connected') return;
    if (!init) {
        init = Date.now();
    }

    const current = Date.now(),
        diff = current - init;

    $('.call-time').text(formatTime(diff));


    setTimeout(() => {
        if (callStatus === 'connected') {
            showCallTime(init);
        }
    }, 1000);
}

function toggleMute() {
    if (['connected', 'connecting', 'reconnecting'].includes(callStatus) && localStream) {
        localStream.getAudioTracks().forEach(track => {
            track.enabled = !isMuted;
        });
        isMuted = !isMuted;
        if (isMuted) {
            $("#muteCallBtn i")[0].classList = 'ri-mic-line fw-lighter'
        } else {
            $("#muteCallBtn i")[0].classList = 'ri-mic-off-line fw-lighter'
        }
    }
}

// CLOSE THE RTC CONNECION

function closePeerConnection() {
    if (peer) {
        peer.ontrack = null
        peer.onconnectionstatechange = null
        peer.oniceconnectionstate = null;
        peer.onicecandidate = null;

        peer.close();
        peer = null;
    }
    return true;
}

function displayCallEnded() {
    showPopup('popup-call-ended');
    $(".call-status").html(`<span class='text-muted'>call declined</span>`);
    $('.btn-decline-call').remove()
}

function reconnectCall() {
    callStatus = 'reconnecting';
    $(".call-status").html(callStatus);
    closePeerConnection();
    peer = initCall()
    if (peer) {
        initVoiceOffer();
    }
}


function hangupCall() {
    callStatus = 'ended';
    isCallEnded = true;
    closePeerConnection();
    displayCallEnded();
    stopStream(localStream);
    if (isRecording) {
        stopRecorder();
    }

    removeParam('type');

    $('body').append(`<script src="${ORIGIN}/js/notifyCall.js"></script>`)
}

function recorderCallback(uri) {
    if (uri) {
        $("#saveCallBtn").removeClass("d-none").attr('href', uri)
        $("#saveCallBtn").attr('download', `connector_recording_${Date.now()}.webm`);
    }
}

async function messageHandler(e) {
    const data = JSON.parse(e.data),
        type = data.type,
        from = data.from,
        to = data.to;

    if (to != userId) {
        return;
    }

    if (type == 'incomming') {
        if (isCallEnded) {
            return;
        }

        let expires = data.expires;
        if (Date.now() < expires) {
            fetchRemoteOffers({
                from: remoteUser,
                to: userId
            });
        } else {
            $(".call-end-err").removeClass('d-none').text('the call has been ended !');
            hangupCall();
        }
        return;
    }

    if (type == 'reconnect') {
        reconnectCall();
        return;
    }

    if (type == 'hangup') {
        callStatus = null;
        hangupCall();
        return;
    }

    if (type == 'reject') {
        $(".call-end-err").removeClass('d-none').text('user didnt answer your call');
        hangupCall();
        return;
    }

    if (type == 'offer') {
        if (data.sdp) {
            await peer.setRemoteDescription(new RTCSessionDescription(data));
            const stream = await initOffer({ audio: true, video: isVideoCall }, !isCaller);
            if (stream && isVideoCall) {
                localVideo.srcObject = stream;
                $('.local-stream-container').html(localVideo)
            }
        } else {
            throwErr('Failed to add caller');
        }
        return;
    }

    if (type === 'answer') {
        await peer.setRemoteDescription(data);
        return;
    }

    if (type === 'candidate') {
        if (data.candidates.length > 0) {
            data.candidates.map(async (candidate) => {
                if (candidate) {
                    await peer.addIceCandidate(candidate)
                }
            })
        } else {
            throwErr('Failed to connect caller');
        }
        return;
    }

    if (type == 'error') {
        $(".call-end-err").removeClass('d-none').text(data.error);
        hangupCall();
        return;
    }
}

// documents handlers

$(function () {
    $(".btn-decline-call").click(function () {
        $(this)[0].disabled = true;
        sendHangupReq();
        hangupCall();
    })

    $(window).on('beforeunload', function () {
        if (peer) {
            if (peer.connectionState == 'connected') {
                $(".call-status").text('reconnecting');
                sendReconnectReq();
            } else {
                sendHangupReq();
            }
        }
    })

    $("#muteCallBtn").click(toggleMute)
})
