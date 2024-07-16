let peer;
$(function () {
    // answer call
    if ([wss, userId, remoteUser].includes(null, undefined, '')) {
        throwErr('An Internal Error Occured !')
        // handleInternalError();
    } else {

        try {
            let remoteAudio = $("#remoteAudio")[0];

            wss.addEventListener('open', async () => {
                peer = initCall(remoteAudio);

                if (!peer) {
                    alert('error intitiating a call');
                }
            })
            wss.addEventListener('message', messageHandler);
        } catch (e) {
            throwErr("An Error Occured, " + e)
        }
    }
})