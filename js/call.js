const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] },
    constraints = { audio: true };
let peer,
    remoteAudio = $("#remoteAudio"),
    callExpiry = null;

$(function () {
    remoteAudio[0].muted = true
    try {
        if ([userId, remoteUser, wss].includes(null, undefined, '')) {
            throwErr('An Un-Expected Error Encountered, Cannot Initiate Call')
            displayCallEnded();
            return;
        };

        async function startCall() {
            peer = createPeer(configuration);

            // track change
            let remoteStream,
                localStream;

            peer.addEventListener('track', (e) => {
                remoteStream = handleTrack(e, remoteAudio[0]);
            });

            peer.onconnectionstatechange = function () {
                const state = peer.connectionState;
                displayCallStatus(state);
                if (state == 'connected') {
                    callStatus = state;
                    startRecording(localStream, remoteStream);
                    displayCallTime();
                }

                if (['disconnected', 'failed'].includes(state)) {
                    callStatus = null;
                    init = null;
                    hangupCall();
                }
            };

            peer.addEventListener("iceconnectionstatechange", () => handleIceConnectionState(peer));


            // ICE candidate
            peer.addEventListener('icecandidate', (e) => handleIceCandidate(e, userId,remoteUser, true))

            // get Media
            localStream = await getMedia(peer, constraints);

            // create a offer
            callExpiry = await createOffer(peer, {
                from: userId,
                to: remoteUser
            }, { iceRestart: true, offerToRecieveAudio: true });
        }

        wss.addEventListener('message', async function (e) {
            const data = JSON.parse(e.data);

            if (data.to != userId) {
                return;
            }

            if (data.type === 'callstatus') {
                throwErr(`The user is ${data.status}. Please try again later.`);
                hangupCall();
                return;
            }

            if (data.type === 'reject') {
                hangupCall('reject');
                return;
            }

            if (data.type === 'hangup') {
                hangupCall();
                return;
            }

            if (data.type === 'answer') {
                if (peer) {
                    await setRemotePeer(peer, data);
                }
                return;
            }

            if (data.type === 'candidate') {
                if (peer) {
                    await addRemoteIceCandidates(peer, data.candidate);
                }
            }
        });

        wss.addEventListener('open', function () {
            startCall();
        });
    } catch (e) {
        throwErr("Caller: An error occurred: " + e, true);
    }
});
