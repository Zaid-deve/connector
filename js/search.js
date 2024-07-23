$(function () {
    const searchContainer = $("#userSearchForm"),
        searchinp = $("#userSearchInp"),
        searchToggler = $('.btn-toggle-search'),
        searchErr = searchinp.next(),
        listOuter = $('.friends-list-box')

    let list = $(".friends-list");

    searchToggler.click(function () {
        searchContainer.slideToggle(250, function () {
            if (searchContainer.is(":visible")) {
                searchinp.focus();
            }
        });
    });

    let curr = listOuter.html();
    let debounceTimer;
    const debounceDelay = 200;

    searchinp.on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const qry = searchinp.val();
            searchErr.text('')
            if (!qry) {
                listOuter.html(curr);
                return;
            }

            $.post(`${ORIGIN}/php/searchHandler.php`, { qry }, function (resp) {
                const r = JSON.parse(resp);
                if (r.Success) {
                    const users = r.Users;
                    if (users.length) {
                        if (!list.length) {
                            listOuter.html("<ul class='list-group friends-list'></ul>");
                            list = $(".friends-list");
                        }
                        users.forEach((u) => {
                            let peer = u.peer,
                                name = u.name,
                                profile = u.profile,
                                el = list.find(`[data-username="${peer}"]`);

                            if (el.length) {
                                el.prependTo(list);
                            } else {
                                list.prepend(`<li class='list-group-item border-0 rounded-0 friend-list-item py-3' data-username='${peer}' data-isstar='0' data-isblocked='0' onclick=\"previewCallOptions('${peer}','${qry}', '${name}', '${profile}')\">
                                                 <div class='d-flex align-items-center gap-2'>
                                                     <img src='${profile}' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                                     <div class='friend-info flex-shrink-0'>
                                                         <div class='fw-bold friend-username'> @${qry}</div>
                                                         <small class='text-muted fw-light'>${name}</small>
                                                     </div>
                                                     <div class='ms-auto text-center list-item-right'>
                                                         <small class='text-muted mt-1 d-none'></small>
                                                     </div>
                                                 </div>
                                             </li>`);
                            }
                        })
                    } else {
                        searchErr.text('no user found for ' + qry);
                    }
                }
            });
        }, debounceDelay);
    });
})