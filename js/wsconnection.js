let wss,
    wssErr;

function connectUser(userId) {
    if (userId) {
        const data = {
            type: 'connection',
            userId: userId
        }

        wss.send(JSON.stringify(data))
        return true;
    }
}

$(function () {
    if(!userId){
        throwErr('Something Went Wrong, No Connection Could Be Made To Server !');
        return;
    }

    // wss connection
    let ws_server = location.host;
    wss = new WebSocket(`wss://${ws_server}:11001`);
    wss.addEventListener('open', () => {
        connectUser(userId);
    })

    wss.addEventListener('close', (e) => {
        if (e.code === 1006) {
            wssErr = e.reason || 'connection closed explicitly !';
        }
    })

    wss.addEventListener('error', (e) => {
        wssErr = e;
    })
})