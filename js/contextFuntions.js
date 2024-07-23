let deleteFriend,
    addStarFriend,
    blockFriend;

$(function () {
    async function xhrReq(url, data) {
        let reqOk = false;
        try {
            await $.post(url, data, function(resp) {
                const r = JSON.parse(resp)
                if (r.Success) {
                    reqOk = true
                    return;
                }

                throwErr(r.Err);
            });
        } catch { }
        return reqOk;
    }
    deleteFriend = async function (username) {
        return await xhrReq(`${ORIGIN}/php/user/deleteFriend.php`, { username });
    }

    addStarFriend = async function (username) {
        return await xhrReq(`${ORIGIN}/php/user/addStarFriend.php`, { username });
    }

    blockFriend = async function (username) {
        return await xhrReq(`${ORIGIN}/php/user/blockFriend.php`, { username });
    }
})