function showPopup(target) {
    let tElm = $(`.popup-container .popup.${target}`);
    if (tElm.length) {
        closePopup();
        $(`.popup-container`).removeClass('d-none')
        tElm.addClass('show');
    }
}

function closePopup() {
    $('.popup-container .popup').removeClass('show')
    $('.popup-container').addClass('d-none');
}

function previewCallOptions(peer, username, name, profile) {
    if (wss && wss.readyState == 0) {
        return;
    }

    if (!wss || wss.readyState != 1 || !isUserConnected) {
        showPopup('popup-connect-error');
        return;
    }

    if (peer) {
        showPopup('popup-make-call');
        $(".remote-username").text(`@${username}`);
        $(".remote-name").text(name);
        $("#__btn__voice__call").click(function () {
            location.href = `https://${location.host}/connector/app/call/voice.php?userId=${peer}`;
        })

        $("#__btn__video__call").click(function () {
            location.href = `https://${location.host}/connector/app/call/video.php?userId=${peer}`;
        })
    }
}

$(function () {
    $(".popup-outer").on('click', function (e) {
        let target = $(e.target);
        if (target.hasClass('popup') && target.hasClass('popup-dismisable')) {
            closePopup()
        }
    })

    $(".btn-toggle-sendreq").click(function () {
        $('.requests-menu-container').removeClass('show');
        $(".__friend__id__err").text('')
        showPopup('popup-add-friend')
        $("#__friend__id__inp").val('').focus();
    })
})