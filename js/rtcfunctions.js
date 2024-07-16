const iceServers = [
    { urls: "stun:stun.l.google.com:19302" },
    { urls: "stun:stun1.l.google.com:19302" },
    { urls: "stun:stun2.l.google.com:19302" },
    { urls: "stun:stun3.l.google.com:19302" },
    { urls: "stun:stun4.l.google.com:19302" }
],
    callConfig = {
        from: userId,
        to: remoteUser,
    };

let isCaller = false,
    callInitTime = null,
    callStatus = null,
    localStream = null,
    remoteStream = null,
    isRecording = false,
    isMuted = false;

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

async function getMedia(peer, constraints) {
    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    const permissionStatus = await navigator.permissions.query({ name: constraints.audio ? 'microphone' : 'camera' });
    if (stream && permissionStatus.state === 'granted') {
        stream.getTracks().forEach(track => {
            peer.addTrack(track, stream);
        });
        return stream;
    }
    return false;
}

function trackHandler(e, destination) {

    let stream = null;
    if (e.streams && e.streams[0]) {
        stream = e.streams[0];
    } else {
        stream = new MediaStream();
        stream.addTrack(e.track);
    }

    if (destination) {
        destination.srcObject = stream;
    }
    return stream;

}

function handleConnectionState(e) {
    let state = e.target.connectionState,
        style = 'muted';

    if (state === 'connected') {
        initRecorder(localStream, remoteStream, recorderCallback);
        isRecording = true;
        callStatus = 'connected';
        style = 'success';
        showCallTime(callInitTime);
    } else if (state == 'disconnected' || state == 'failed') {
        callStatus = null;
        style = 'danger'
        state = 'ended';
    } else {
        state = 'connecting...';
    }

    $(".call-status").html(`<span class='text-${style}'>${state}</span>`);
}

function handleIceConnectionState(e) {
    const state = e.target.iceConnectionState;
    if (state == 'failed') {
        e.target.restartIce();
    }
}

async function createOffer(peer, callConfig, configuration = {}) {
    const offer = await peer.createOffer(configuration);
    await peer.setLocalDescription(offer);

    if (offer.sdp) {
        let expires = Date.now() + 40000;
        offerData = { type: 'offer', sdp: offer.sdp, ...callConfig, 'expires': expires }
        wss.send(JSON.stringify(offerData));
        return expires;
    }
    return false;
}



const candidatesToSend = [];
function sendIceCandidates(e, callConfig) {
    if (e.candidate) {
        candidatesToSend.push(e.candidate);
    } else {
        const candidate = {
            type: 'candidate',
            candidates: candidatesToSend,
            ...callConfig
        }
        wss.send(JSON.stringify(candidate))
        return false;
    }
    return true;
}

async function createAnswer(peer, callConfig, configuration = {}) {
    const answer = await peer.createAnswer(configuration)
    await peer.setLocalDescription(answer);

    if (answer) {
        const answerData = {
            type: 'answer', sdp: answer.sdp, ...callConfig
        }
        wss.send(JSON.stringify(answerData));
        return true;
    }
    return false;
}

function fetchRemoteOffers(callConfig) {
    const data = {
        type: 'fetch-offer',
        ...callConfig,
    }
    wss.send(JSON.stringify(data))
    return true;
}

function initCall(destination) {
    peer = new RTCPeerConnection(iceServers);

    // track
    peer.ontrack = function (e) {
        remoteStream = trackHandler(e, destination);
    }

    // send ice candidates
    peer.addEventListener('icecandidate', (e) => {
        sendIceCandidates(e, { ...callConfig, isCaller: isCaller });
    })

    // state change
    peer.addEventListener('connectionstatechange', handleConnectionState);
    peer.addEventListener('iceconnectionstatechange', handleIceConnectionState);

    return peer;
}

async function initVoiceOffer() {
    localStream = await getMedia(peer, { audio: true });

    if (localStream) {
        const offerConfig = {
            offerToRecieveAudio: true,
            iceRestart: true
        }

        callConfig.callType = 'audio';
        await createOffer(peer, callConfig, offerConfig);
    } else {
        alert('permission denied')
    }
}

function toggleMute() {
    if (localStream) {
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


async function initVoiceAnswer() {
    localStream = await getMedia(peer, { audio: true });

    if (localStream) {
        const offerConfig = {
            offerToRecieveAudio: true
        }

        callConfig.callType = 'audio';
        await createAnswer(peer, callConfig, offerConfig);
    } else {
        alert('permission denied')
    }
}

function closePeerConnection() {
    if (peer) {
        peer.ontrack = null
        peer.onconnectionstatechange = null
        peer.oniceconnectionstate = null;
        peer.onicecandidate = null;

        peer.close();
        peer = null;
    }
    $('.ui-box').html('<p class="text-warning">call has ended</p>');
    return true;
}

function displayCallEnded() {
    showPopup('popup-call-ended');
}

function sendHangupReq() {

    let data = {};
    if (isCaller) {
        data = { ...callConfig };
    } else {
        data.from = remoteUser
        data.to = userId;
    }
    data.type = 'hangup';
    data.isCaller = isCaller;

    wss.send(JSON.stringify(data));

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

function sendReconnectReq() {
    callStatus = 'ended';
    let data = {};
    if (isCaller) {
        data = { ...callConfig };
    } else {
        data.from = remoteUser
        data.to = userId;
    }
    data.type = 'reconnect';
    wss.send(JSON.stringify(data));
}

function hangupCall() {
    callStatus = 'ended';
    closePeerConnection();
    displayCallEnded();
    stopStream(localStream);
    if (isRecording) {
        stopRecorder();
    }
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
        let expires = data.expires;
        if (Date.now() < expires) {
            fetchRemoteOffers({
                from: remoteUser,
                to: userId
            });
        } else {
            // HandleCallEnded()
            alert('call ended');
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
            await initVoiceAnswer();
        } else {
            alert('error adding remote description');
        }
        return;
    }

    if (type === 'answer') {
        await peer.setRemoteDescription(data);
    }

    if (type === 'candidate') {
        if (data.candidates.length > 0) {
            data.candidates.map(async (candidate) => {
                if (candidate) {
                    await peer.addIceCandidate(candidate)
                }
            })
        } else {
            alert('error adding remote ice candidates');
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

function stopStream(stream) {
    stream.getTracks().forEach(track => {
        track.stop();
    })
}