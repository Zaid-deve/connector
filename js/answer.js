const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] },
    constraints = { audio: true };
let pc, remoteIceCandidates = [],
    remoteAudio = $("#remoteAudio");

$(function () {
    try {
        // remoteAudio[0].muted = true
        if ([wss, remoteUser, userId].includes(null, undefined, '')) return;

        function fetchRemoteOffers() {
            const data = {
                type: 'fetch-offer',
                from: remoteUser,
                to: userId
            };
            wss.send(JSON.stringify(data))
            return true;
        }

        function fetchRemoteCandidates() {
            const data = {
                type: 'fetch-candidate',
                from: remoteUser,
                to: userId
            }
            wss.send(JSON.stringify(data))
            return true;
        }

        async function getMedia() {
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            if (stream && stream.active) {
                stream.getTracks().forEach(track => {
                    pc.addTrack(track, stream);
                });
            }
            return stream;
        }

        async function createAnswer() {
            const answer = await pc.createAnswer({ iceRestart: true });
            await pc.setLocalDescription(answer);

            const answerWithMetadata = {
                type: answer.type,
                sdp: answer.sdp,
                from: userId,
                to: remoteUser
            };
            wss.send(JSON.stringify(answerWithMetadata));
        }

        async function startCall() {
            pc = new RTCPeerConnection(configuration);

            // track change
            let remoteStream,
                localStream;

            pc.ontrack = function (e) {
                if (e.streams && e.streams[0]) {
                    remoteAudio[0].srcObject = e.streams[0];
                    remoteStream = e.streams[0];
                } else {
                    remoteStream = new MediaStream();
                    remoteStream.addTrack(e.track);
                    remoteAudio[0].srcObject = remoteStream;
                }
            };

            pc.onconnectionstatechange = function () {
                const state = pc.connectionState;
                displayCallStatus(state);
                if (state == 'connected') {
                    startRecording(localStream, remoteStream);
                    displayCallTime();
                }
            };

            pc.oniceconnectionstatechange = () => {
                if (pc.iceConnectionState === "failed") {
                    pc.restartIce();
                }
            };

            // ICE candidate
            pc.addEventListener('icecandidate', function (e) {
                if (e.candidate) {
                    const candidate = {
                        type: 'candidate',
                        candidate: e.candidate,
                        from: userId,
                        to: remoteUser,
                        caller: false
                    };
                    wss.send(JSON.stringify(candidate));
                }
            });

            localStream = await getMedia();
        }

        function pushRemoteCandidates() {
            remoteIceCandidates.forEach(async (c) => {
                if (c) {
                    try {
                        await pc.addIceCandidate(new RTCIceCandidate(c));
                    } catch (error) {
                        throwErr('Receiver: Error adding remote ICE candidate:', error);
                    }
                }
            });
        }

        wss.addEventListener('open', async function () {
            await startCall();
            fetchRemoteOffers();
        });

        wss.addEventListener('message', async function (e) {
            const data = JSON.parse(e.data);

            if (data.type === 'reconnect') {
                displayCallStatus('re-connecting');
                return;
            }

            if (data.type === 'hangup') {
                hangupCall();
                return;
            }

            if (data.type === 'offer') {
                await pc.setRemoteDescription(new RTCSessionDescription(data));
                await createAnswer();
                if (fetchRemoteCandidates()) {
                    pushRemoteCandidates();
                }
                return;
            }

            if (data.type === 'candidate' && data.candidate) {
                if (pc.remoteDescription && pc.remoteDescription.type) {
                    try {
                        await pc.addIceCandidate(new RTCIceCandidate(data.candidate));
                    } catch (error) {
                        throwErr('Receiver: Error adding received ICE candidate:', error);
                    }
                } else {
                    remoteIceCandidates.push(data.candidate);
                }
            }
        });

        window.onbeforeunload = function () {
            if (pc && pc.connectionState == 'connected') {
                sendReconnectReq(remoteUser, userId)
            }
        }
    } catch (e) {
        console.error("Receiver: An error occurred:", e);
    }
});
