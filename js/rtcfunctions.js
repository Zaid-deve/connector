function createPeer(configuration) {
    return new RTCPeerConnection(configuration);
}

function handleTrack(data, destination) {
    let stream;
    if (data.streams && data.streams[0]) {
        destination.srcObject = data.streams[0];
        stream = data.streams[0];
    } else {
        stream = new MediaStream();
        remoteStream.addTrack(data.track);
        destination.srcObject = remoteStream;
    }
    return stream;
}

function handleIceConnectionState(peer) {
    if (peer.iceConnectionState === "failed") {
        peer.restartIce();
    }
}

function handleIceCandidate(candidates, from, to, isCaller = false) {
    if (candidates.candidate) {
        const candidate = {
            type: 'candidate',
            candidate: candidates.candidate,
            from: from,
            to: to,
            caller: isCaller
        };
        wss.send(JSON.stringify(candidate));
    }

    return true;
}

async function getMedia(peer, constraints) {
    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    if (stream && stream.active) {
        stream.getTracks().forEach(track => {
            peer.addTrack(track, stream);
        });
    }
    return stream;
}

async function createOffer(peer, info, configuration = {}, expires = Math.floor(Date.now() / 1000 + 40)) {
    const offer = await peer.createOffer(configuration);
    await peer.setLocalDescription(offer);

    const offerWithMetadata = {
        type: 'offer',
        sdp: offer.sdp,
        from: info.from,
        to: info.to,
        callType: configuration?.offerToRecieveVideo ? 'video' : 'audio',
        expires: expires
    };
    wss.send(JSON.stringify(offerWithMetadata));
    return expires;
}

async function setRemotePeer(peer, description) {
    await peer.setRemoteDescription(new RTCSessionDescription(description));
    return true;
}

async function addRemoteIceCandidates(peer, candidate) {
    await peer.addIceCandidate(new RTCIceCandidate(candidate));
    return true;
}