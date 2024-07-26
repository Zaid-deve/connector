let toggleContextMenu;

$(function () {
    let ctTarget, ctElm, isStar = isBlocked = false;
    const menu = $(".chat-context-menu"),
        listContainer = $(".friends-list-outer");

    function getClickCoords(e) {
        const { clientX, clientY } = e,
            pwidth = listContainer.width(), pheight = listContainer.height(),
            mheight = menu.height(), mwidth = menu.width();

        let x = clientX;
        if (x + mwidth > pwidth) {
            x = pwidth - mwidth - 40;
        }

        let y = clientY;
        if (y + mheight > pheight) {
            y = pheight - mheight;
        }

        let coords = { x, y };
        return coords;
    }

    toggleContextMenu = function (e) {
        e.preventDefault();
        menu.removeClass('d-none');
        const coords = getClickCoords(e);
        menu.css({ top: `${coords.y}px`, left: `${coords.x}px` });
        // get current target username
        ctElm = $(e.target).closest('.friend-list-item')
        ctTarget = ctElm.data('enc-username');
        isStar = ctElm.data('isstar');
        isBlocked = ctElm.data('isblocked');

        if (isBlocked) {
            $(".context-menu-block-btn .chat-context-text").text('Unblock friend')
        } else {
            $(".context-menu-block-btn .chat-context-text").text('Block Friend')
        }

        if (isStar) {
            $(".context-menu-star-btn .chat-context-text").text('Remove From Star Friend')
        } else {
            $(".context-menu-star-btn .chat-context-text").text('Mark Star Friend')
        }
    };

    $(".context-menu-delete-btn").click(async function () {
        if (ctTarget) {
            if (await deleteFriend(ctTarget)) {
                if (ctElm) {
                    alert('elm deleted');
                    ctElm.remove();
                    closeMenu();
                    if (!$('.friends-list').children().length) {
                        $('.friends-list').html(`<div class='py-5 px-3 d-flex justify-content-center flex-column gap-4'>
                                                    <img src='../images/empty.png' class='img-contain d-block mx-auto' style='max-height:180px;'>
                                                    <small class='text-muted fw-light text-center'>add friends to quikly <br> make a video/audio call with them.</small>
                                                 </div>`)
                    }
                }
            } else {
                throwErr('failed to delete friend');
            }
        }
    })

    $(".context-menu-star-btn").click(async function () {
        if (ctTarget) {
            if (await addStarFriend(ctTarget)) {
                if (ctElm) {
                    let starFriendicon;
                    if (isStar) {
                        starFriendicon = ctElm.find('.star-friend-icon')
                        ctElm.data('isstar', 0);
                        if (starFriendicon.length) {
                            starFriendicon.remove()
                        }
                    } else {
                        ctElm.data('isstar', 1);
                        starFriendicon = "<div class='star-friend-icon'> <i class='ri-star-smile-fill'></i> </div>";
                        ctElm.find('.list-item-right').prepend(starFriendicon);
                    }
                }
                closeMenu();
            } else {
                throwErr('failed to mark friend as star');
            }
        }
    })

    $(".context-menu-block-btn").click(async function () {
        if (ctTarget) {
            if (await blockFriend(ctTarget)) {
                if (isBlocked) {
                    ctElm.data('isblocked', 0);
                    ctElm.find('.friend-username').removeClass('text-danger').find('i').remove();
                } else {
                    ctElm.data('isblocked', 1);
                    ctElm.find('.friend-username').addClass('text-danger').prepend('<i class="ri-prohibited-2-line"></i>')
                }
                closeMenu()
            }  else {
                throwErr('failed to block friend');
            }
        }

    })

    function closeMenu() {
        menu.addClass('d-none');
        ctElm = null;
        ctTarget = null;
        isStar = false;
    }

    $(window).click(function (e) {
        if (!e.target.closest('.chat-context-menu')) {
            closeMenu()
        }
    });
});
