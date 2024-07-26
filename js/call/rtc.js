/* 

==============================

RTC FUNCTIONS

=============================

*/

// DEFAULTS
const candidatesToSend = [];


// GET USER MEDIA

async function getMedia(peer, constraints) {
    const audioPerm = constraints.audio ? await hasPerm('microphone') : true,
        videoPerm = constraints.video ? await hasPerm('camera') : true;
    if (!audioPerm && !videoPerm) {
        return 'PERM_ERR';
    }

    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    if (stream) {
        stream.getTracks().forEach(track => {
            peer.addTrack(track, stream);
        });
        return stream;
    }
    return null;
}


// PREAPRE OFFER

async function createOffer(callConfig, configuration = {}) {
    const offer = await peer.createOffer(configuration);
    await peer.setLocalDescription(offer);

    if (offer.sdp) {
        let expires = callInitTime + 40000,
            callType = configuration.offerToReceiveVideo ? 'video' : 'audio';

        offerData = { type: 'offer', sdp: offer.sdp, ...callConfig, callType, expires }
        wss.send(JSON.stringify(offerData));
        return expires;
    }

    return false;
}

// SEND ICE CANDIDATES

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


// PREAPRE AN ANSWER 

async function createAnswer(callConfig, configuration = {}) {
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

// FETCH INCOMMINGS/OFFERS

function fetchRemoteOffers(callConfig) {
    const data = {
        type: 'fetch-offer',
        ...callConfig,
    }
    wss.send(JSON.stringify(data))
    return true;
}

// INITIALIZE A CALL

function initCall(destination) {
    peer = new RTCPeerConnection(iceServers);

    // track
    peer.addEventListener('track', function (e) {
        remoteStream = trackHandler(e);
        if (remoteStream) {
            destination.srcObject = remoteStream;
        }
    })

    // send ice candidates
    peer.addEventListener('icecandidate', (e) => {
        sendIceCandidates(e, { ...callConfig, isCaller: isCaller });
    })

    // state change
    peer.addEventListener('connectionstatechange', handleConnectionState);
    peer.addEventListener('iceconnectionstatechange', handleIceConnectionState);

    callInitTime = Date.now();
    startTimeout(callInitTime);
    return peer;
}

// END A STREAM

function stopStream(stream) {
    stream.getTracks().forEach(track => {
        track.stop();
    })
}

// HANGUP CALL FUNCTIONS

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

// RECONNECTING FUNCTIONS

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

// MAKE OFFER FUNCTION

async function initOffer(constraints, isAnswerer = false) {

    if (peer) {
        localStream = await getMedia(peer, constraints);
        if (localStream == 'PERM_ERR') {
            localStream = null;
            alert('please enable your device ' + constraints.audio ? 'microphone' : 'camera' + ' to connect to the call');
            return false;
        }

        if (localStream) {
            let config = {}, offer;
            config.offerToReceiveAudio = constraints.audio;
            config.offerToReceiveVideo = constraints.video;
            config.isCaller = !isAnswerer

            if (isAnswerer) {
                offer = await createAnswer(callConfig, config);
            }
            else {
                offer = await createOffer(callConfig, config);
            }

            if (offer) {
                return localStream;
            }
        }
    }
    return false;

}