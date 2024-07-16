function showPopup(target) {
    if (target) {
        $(".popup-container").removeClass('d-none')
        $(`.${target}`).removeClass('d-none')
    }
}

function closePopup(target) {
    let toClose = null;

    if (target instanceof HTMLElement) {
        toClose = $(target);
    } else if (typeof target == 'string') {
        toClose = $(`.${target}`);
    }

    if (toClose) {
        let isDismisable = toClose.hasClass('popup-dismisable')
        if (isDismisable) {
            $('.popup-container').addClass('d-none');
            toClose.addClass('d-none')
        }
    } else {
        $('.popup-container').addClass('d-none');
        $('.popup-container .popup').addClass('d-none')
    }

}

function previewCallOptions(peer, username, name, profile) {
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
    $(".popup-container").on('click', function (e) {
        if ($(e.target).hasClass('container-fluid')) {
            const visiblePopup = $(this).find('.popup:not(.d-none)');
            if (visiblePopup.length) {
                closePopup(visiblePopup[0]);
            } else closePopup();
        }
    })

    $(".btn-toggle-sendreq").click(function () {
        $('.requests-menu-container').removeClass('show');
        $(".__friend__id__err").text('')
        showPopup('popup-add-friend')
        $("#__friend__id__inp").val('').focus();
    })
})