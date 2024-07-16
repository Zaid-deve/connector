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

        try {
            await $.post("../php/handleRequest.php", { userId }, function (resp) {
                const r = JSON.parse(resp);
                if (r.Success) {
                    closePopup('popup-add-friend')
                    return;
                }

                if (r.IdErr) {
                    friendIdErr.text(r.IdErr)
                }
                else {
                    throwErr(r.Err || 'Something Went Wrong !');
                }
            })
        } catch (e) {
            throwErr('Something Went Wrong');
        }

        friendId.prop('readonly', false);
        sendReqbtn.prop('disabled', false);
    })
})