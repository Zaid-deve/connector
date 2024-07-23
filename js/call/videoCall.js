// video buffers
const localVideo = document.createElement('video'),
    remoteVideo = document.createElement('video');

let isControlsHidden = false;

$(function () {
    // voice call
    if ([wss, userId, remoteUser].includes(null, undefined, '')) {
        throwErr('An Internal Error Occured !')
        // handleInternalError();
    } else {
        try {
            isVideoCall = true;

            localVideo.autoplay = true;
            localVideo.muted = true;
            localVideo.id = "localStream";

            remoteVideo.autoplay = true;
            remoteVideo.volume = 1;
            remoteVideo.id = "remoteStream";

            function toggleControls() {
                let callback;
                if (!isControlsHidden) {
                    callback = 'slideDown'
                    $('.local-stream-container').removeClass('shrink')
                    isControlsHidden = true
                } else {
                    callback = 'slideUp';
                    $('.local-stream-container').addClass('shrink')
                    isControlsHidden = false
                }
                $("#callHeader,#callFooter")[callback](250)
            }

            $("#videoContainer").click(() => toggleControls(false))

            // start call
            wss.addEventListener('open', async () => {
                peer = initCall(remoteVideo);


                if (!peer) {
                    throwErr("Failed To Initiate A Call");
                    return;
                }

                peer.addEventListener('connectionstatechange', function () {
                    if (peer.connectionState == 'connected') {
                        toggleControls(true);
                        $('.remote-stream-container').html(remoteVideo);
                    }
                })

                if (isCaller) {
                    let stream = await initOffer({ audio: true, video: true })
                    if (!stream) {
                        alert('an error accured, cannot preapre call');
                    } else {
                        localVideo.srcObject = stream;
                        $('.local-stream-container').html(localVideo);
                    }
                }

            });

            // message handler
            wss.addEventListener('message', (e) => {
                messageHandler(e)
            });

        } catch (e) {
            throwErr('An Internal Error Occured, ' + e);
        }
    }
})