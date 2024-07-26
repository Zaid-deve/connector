// searcbar

async function toggleSendReq(ev) {
    ev.stopPropagation();
    const btn = $(ev.target).closest('.btn');
    btn[0].disabled = true;
    if (await sendFriendRequest($(ev.target).closest('.list-group-item').data('username')) == true) {
        btn.find('small').text('request sent');
    } else {
        throwErr('Failed To Send Request !')
    }
}

$(function () {
    // defaults
    let searchbar = $("#userSearchForm"),
        searchInp = $("#userSearchInp"),
        searchInpErr = searchInp.next(),
        searchTogglerBtn = $(".btn-toggle-search"),
        list = $(".friends-list"),
        prependObj = null;

    searchTogglerBtn.click(function () {
        searchbar.slideToggle(200, function () {
            if (searchbar.is(":visible")) {
                searchInp.focus();
            } else {
                searchInp.val('')
                if (prependObj) {
                    prependObj.remove()
                    prependObj = null;
                }
            }
        })
    })

    function searchSuccess(resp) {
        const r = JSON.parse(resp);
        if (r.Success) {
            const users = r.Users
            if (users.length) {
                users.forEach((r) => {
                    // get info 
                    let username = searchInp.val(),
                        name = r.name,
                        profile = r.profile,
                        peer = r.peer,
                        isFriend = r.isFriend,
                        isRequested = r.isRequested,
                        isStar = r.isStar,
                        isBlocked = r.isBlocked;

                    if(!list.length){
                        $(".friends-list-box").html("<ul class='list-group friends-list'></ul>")
                        list = $(".friends-list")
                    }

                    // if already visible
                    let exi = list.find(`[data-enc-username='${peer}']`);
                    if (exi.length) {
                        exi.prependTo(list)
                        return;
                    }

                    let lst_right = isStar ? "<div class='star-friend-icon'> <i class='ri-star-smile-fill'></i> </div> <small class='text-muted mt-1'>offline</small>" : "<button class='btn border-0'  onclick='toggleSendReq(event)'><small class='text-primary'>+ send request</small></button>",
                        blockStr = isBlocked ? " text-danger'> <i class='ri-prohibited-2-line text-danger'></i> " : "'>",
                        contextEvent = isFriend ? "oncontextmenu='toggleContextMenu(event)'" : '';

                    if (isRequested) {
                        lst_right = "<button class='btn border-0'><small class='text-info fw-light'>request sent</small></button>"
                    }

                    list.prepend(`<li class='list-group-item border-0 rounded-0 friend-list-item py-3 friend-list-item' ${contextEvent} data-enc-username='${peer}' data-username='${username}' data-name='${name}' data-profile='${profile}' data-isfriend='${isFriend}' data-isstar='${isStar}' data-isblocked='${isBlocked}' onclick='showRemoteCaller(event)'>
                                            <div class='d-flex align-items-center gap-2'>
                                                <img src='${profile}' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                                <div class='friend-info flex-shrink-0'>
                                                    <div class='fw-bold friend-username ${blockStr} @${username}</div>
                                                    <small class='text-success fw-light'>${name}</small>
                                                </div>
                                                <div class='ms-auto text-center list-item-right'>
                                                    ${lst_right}
                                                </div>
                                            </div>
                                        </li>`);
                    prependObj = list.children().first()
                })
            } else {
                searchInpErr.text(r.Err || 'no user found')
            }
        }
    }

    // debounce
    let debounceTimer,
        debounceDelay = 200;

    // search
    searchInp.on('input', function () {
        clearTimeout(debounceTimer);
        searchInpErr.text('');
        if (prependObj) {
            prependObj.remove();
            prependObj = null;
        }

        debounceTimer = setTimeout(() => {
            let qry = searchInp.val();

            if (qry) {
                try {
                    $.post(`${ORIGIN}/php/searchHandler.php`, { qry }, searchSuccess).fail(() => searchInpErr.text('something went wrong'));
                } catch {
                    searchInpErr.text('something went wrong !');
                }
            }

        }, debounceDelay);
    })
})