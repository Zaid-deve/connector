let wss,
    wssErr,
    ws_server = location.host,
    isUserConnected = false;

function connectUser() {
    if (userId) {
        const data = {
            type: 'connection',
            userId: userId
        }

        wss.send(JSON.stringify(data))
        isUserConnected = true;
        return isUserConnected;
    }
}

function conenctToSocket(port = 11001) {
    if (wss && wss.readyState == 1) {
        return wss;
    }

    // connect instead
    wss = new WebSocket(`wss://${ws_server}:${port}`);
    return wss;
}

function showSocketConnectionErr() {
    closePopup();
    showPopup('popup-connect-error');
    setTimeout(conenctToSocket, 2500);
}

$(function () {

    // socket connection
    conenctToSocket();
    if (wss) {

        // socket handlers
        wss.addEventListener('open', connectUser)
        wss.addEventListener('error', showSocketConnectionErr)
        wss.addEventListener('close', function (e) {
            if (!e.wasClean) {
                showSocketConnectionErr();
            }
        })

        navigator.onLine = function () {
            if (wss.readyState != 1) {
                conenctToSocket();
                if (wss) {
                    closePopup()
                }
            }
        }

    }

})