async function sendFriendRequest(userId) {
    let rt;
    try {
        await $.post(`${ORIGIN}/php/sendFriendRequest.php`, { userId }, function (resp) {
            const r = JSON.parse(resp)
            if (r.Success) {
                rt = true
            } else rt = r.IdErr || r.Err
        })
    } catch { rt = false }
    return rt;
}

$(function () {
    const friendId = $("#__friend__id__inp"),
        sendReqbtn = $("#__btn__sendreq"),
        friendIdErr = $(".__friend__id__err")

    friendId.on('input', function () {
        if ($(this).val().length >= 6) {
            sendReqbtn.prop('disabled', false)
        } else {
            sendReqbtn.prop('disabled', true)
        }
    })

    sendReqbtn.click(async function () {
        let userId = friendId.val();
        if (!userId) return;

        friendId.prop('readonly', true);
        sendReqbtn.prop('disabled', true);

        let reqStatus = await sendFriendRequest(userId);
        if (reqStatus === true) {
            closePopup();
        } else {
            friendIdErr.text(reqStatus || 'Someting Went Wrong')
        }

        friendId.prop('readonly', false);
        sendReqbtn.prop('disabled', false);
    })
})