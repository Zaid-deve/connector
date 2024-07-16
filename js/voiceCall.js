let peer;
$(function () {
    // voice call
    if ([wss, userId, remoteUser].includes(null, undefined, '')) {
        throwErr('An Internal Error Occured !')
        // handleInternalError();
    } else {
        try {

            let remoteAudio = $("#remoteAudio")[0];

            // start call
            wss.addEventListener('open', async () => {
                isCaller = true;
                peer = initCall(remoteAudio);
                if (peer) {
                    await initVoiceOffer();
                } else {
                    throwErr("Failed To Initiate A Call");
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