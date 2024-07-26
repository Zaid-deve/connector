let wss,
    wss_host = location.host,
    wss_port = 11001,
    init_socket,
    isUserConnected,
    isSocketClosedExplicityly;

// Connect to socket
function socketConnect() {
    try {
        return new WebSocket(`wss://${wss_host}:${wss_port}`);
    } catch {
        return false;
    }
}

function socketClose(socket) {
    isUserConnected = false;
    if (socket && socket.readyState !== WebSocket.CLOSED) {
        socket.close();
    }
    return null;
}

function socketConnectErr() {
    wss = socketClose(wss);
    showPopup('popup-connect-error');
}

function connectUser(socket, userId) {
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            type: 'connection',
            userId: userId
        }));
        isUserConnected = true;
    }
}

function updateUserStatus(socket, userId, status) {
    if (socket && socket.readyState == 1) {
        socket.send(JSON.stringify({
            type: 'update-status',
            userId,
            status
        }))
    }
}

$(function () {
    init_socket = function () {
        wss = socketConnect();
        if (wss) {
            wss.addEventListener('open', () => {
                connectUser(wss, userId)
                if (wss.readyState == 1) {
                    closePopup('popup-connect-error');
                }
            });
            wss.addEventListener('error', socketConnectErr);
            wss.addEventListener('close', (e) => {
                if (!e.wasClean) {
                    socketConnectErr();
                } else {
                    isSocketClosedExplicityly = true;
                }
            });
        } else {
            socketConnectErr();
        }
    }

    function call_init_socket() {
        setTimeout(() => {
            call_init_socket();
        }, 5000)

        if (wss) {
            if (wss.readyState == WebSocket.OPEN || (wss.readyState == WebSocket.CLOSED && isSocketClosedExplicityly)) {
                return;
            }
        }

        init_socket();
    }
    call_init_socket();

    window.addEventListener('beforeunload', (event) => {
        if (isUserConnected) {
            const nextURL = event.target.activeElement.href;
            if (nextURL && !nextURL.includes(location.hostname)) {
                updateUserStatus(socket, userId, 'disconnect');
            } else {
                updateUserStatus(socket, userId, 'in-active');
            }
        }
    });


});
