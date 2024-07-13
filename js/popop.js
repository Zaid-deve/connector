function showPopup(target) {
    if (target) {
        $(".popup-container").removeClass('d-none')
        $(`.${target}`).removeClass('d-none')
    }
}

function closePopup(target) {
    if (target) {
        if ($(`.${target}`).hasClass('popup-dismisable')) {
            $(".popup-container").addClass('d-none');
            $(`.${target}`).addClass('d-none')
        }
    } else {
        $(".popup-container").addClass('d-none');
        $(`.popup-container .popup`).each((i, f) => {
            $(f).addClass('d-none');
        })
    }
}

function previewCallOptions(peer, username, name, profile) {
    if (peer) {
        showPopup('popup-make-call');
        $(".remote-username").text(`@${username}`);
        $(".remote-name").text(name);
        $("#__btn__voice__call").click(function () {
            // if(!isUserAvailabel()){
            //     userNotAvailable(msg);
            //     return
            // }
            location.replace(`https://${location.host}/connector/app/call/voice.php?userId=${peer}`);
        })

        $("#__btn__video__call").click(function () {
            // if(!isUserAvailabel()){
            //     userNotAvailable(msg);
            //     return
            // }
            location.replace(`https://${location.host}/connector/app/call/video.php?userId=${peer}`);
        })
    }
}

$(function () {
    $(".popup-container").on('click', function (e) {
        if ($(e.target).hasClass('container-fluid')) {
            closePopup();
        }
    })

    $(".btn-toggle-sendreq").click(function () {
        $('.requests-menu-container').removeClass('show');
        $(".__friend__id__err").text('')
        showPopup('popup-add-friend')
        $("#__friend__id__inp").val('').focus();
    })
})