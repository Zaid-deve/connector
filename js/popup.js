function showPopup(target) {
    let tElm = $(`.popup-container .popup.${target}`);
    if (tElm.length) {
        closePopup();
        $(`.popup-container`).removeClass('d-none')
        tElm.addClass('show');
    }
}

function closePopup(target) {

    if (target) {
        $(`.popup-container .popup.${target}`).removeClass('show');
    } else {
        $('.popup-container .popup').removeClass('show');
    }

    let vpopups = $('.popup-container .popup.show')
    if (!vpopups.length) {
        $('.popup-container').addClass('d-none')
    }

}

function showRemoteCaller(ev) {
    if (wss && wss.readyState == 0) {
        return;
    }

    if (!wss || wss.readyState != 1 || !isUserConnected) {
        showPopup('popup-connect-error');
        return;
    }

    // get data
    const target = $(ev.target).closest('.friend-list-item');
    if (!target.length) {
        return;
    }

    const peer = target.data('enc-username'),
        username = target.data('username'),
        name = target.data('name'),
        profile = target.data('profile'),
        isFriend = target.data('isfriend');

    if (!isFriend) {
        return;
    }

    if (peer) {
        showPopup('popup-make-call');
        $(".remote-username").text(`@${username}`);
        $(".remote-name").text(name);
        $(".make-call-profile")[0].src = profile;

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