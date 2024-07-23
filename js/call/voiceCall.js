$(function () {
    // voice call
    if ([wss, userId, remoteUser].includes(null, undefined, '')) {
        throwErr('An Internal Error Occured !')
        // handleInternalError();
    } else {
        try {

            let remoteAudio = $("#remoteAudio")[0];
            remoteAudio.volume = 0.6;

            // start call
            wss.addEventListener('open', async () => {
                peer = initCall(remoteAudio);

                if (!peer) {
                    throwErr("Failed To Initiate A Call");
                    return;
                }

                if (isCaller) {
                    if (!await initOffer({ audio: true })) {
                        alert('an error accured, cannot preapre call');
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