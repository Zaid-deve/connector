let requests = [];
$(function () {
    const reqsMenu = $(".requests-menu-container")

    let fetchReq = null,
        lasReqFetchId = 0,
        isReqPending = false,
        newReqsCount = 0,
        reqFetchErr = null,
        reqNode = null;

    $('.btn-toggle-requests').click(function () {
        if (reqsMenu.length) {
            displayRequests()
            $(window).click(function (e) {
                if (!e.target.closest('.requests-menu-container') && reqsMenu.hasClass('show') && !e.target.closest('.btn-toggle-requests')) {
                    reqsMenu.removeClass('show')
                }
            })
            if (!reqsMenu.hasClass('show')) {
                fetchRequests();
            }
            reqsMenu.addClass('show');
            newReqsCount = 0;
            $(".pending-req-badge").addClass('d-none')
        }
    })

    function displayRequests() {
        if (!requests.length) {
            $(".requests-output").html(`<div class='text-center text-muted fw-ligth py-3'><h1><i class="ri-group-line text-muted"></i></h1>${reqFetchErr || 'Something Went Wrong !'}</div>`);
            return;
        }

        let output = "<ul class='list-group requests-list'>",
            lastScroll = 0;

        if ($(".requests-list").length) {
            lastScroll = $(".requests-list")[0].scrollTop;
        }

        requests.forEach((u, i) => {
            if (u !== undefined) {
                output += `<li class="list-group-item border-0 rounded-0 request-item py-3" onclick="showAcceptPopup('${u.username}','${u.name}','${u.profile}','${u.req_time}', this, '${i}')">
                            <div class="d-flex align-items-center gap-2">
                                <img src="${u.profile}" alt="#" class="rounded-circle img-cover bg-secondary flex-shrink-0" height="40" width="40">
                                <div class="requester-info flex-shrink-0">
                                    <div class="fw-bold">@${u.username}</div>
                                    <small class="text-muted">${u.name}</small>
                                </div>
                                <div class="ms-auto">
                                    <small class="text-muted">${u.req_time}</small>
                                </div>
                            </div>
                        </li>`
            }
        });
        output += '</ul>';
        $(".requests-output").html(output);
        $(".requests-output")[0].scrollTo({ top: lastScroll })
    }

    if (reqsMenu.length) {
        reqsMenu.blur(function () {
            reqsMenu.removeClass('show')
        })

        callFetchRequests();
    }

    function callFetchRequests() {
        fetchRequests();
        fetchReq = setTimeout(() => {
            callFetchRequests();
        }, 5000);
    }

    // fetch requests
    function fetchRequests() {
        if (isReqPending) {
            return;
        }

        isReqPending = true;
        $.get(`../php/fetchRequests.php?end=${lasReqFetchId}`, function (resp) {
            const r = JSON.parse(resp);
            if (r.Success) {
                if (r.Users.length) {
                    lasReqFetchId = r.end || 0;
                    requests = [...requests, ...r.Users]
                    if (reqsMenu.hasClass('show')) {
                        displayRequests();
                        return;
                    }

                    newReqsCount = r.Users.length;
                    $(".pending-req-badge").removeClass('d-none').text(`+ ${newReqsCount}`)
                }
            } else {
                reqFetchErr = r.Err || 'Something Went Wrong !';
            }
        }).always(function () {
            isReqPending = false;
        })
    }

})

function showAcceptPopup(username, name, profileSrc, req_time, reqNode, i) {
    showPopup("popup-accept-req");
    $(".popup-accept-req-header small").html(`Accept Friend Request From ${name}`)
    $(".req-info-body").html(`<div class="d-flex align-items-center gap-2">
                                  <img src="${profileSrc}" alt="#" class="rounded-circle img-cover bg-secondary flex-shrink-0" height="40" width="40">
                                  <div class="requester-info flex-shrink-0">
                                      <div class="fw-bold">@${username}</div>
                                      <small class="text-muted">${name}</small>
                                  </div>
                                  <div class="ms-auto">
                                      <small class="text-muted">${req_time || ''}</small>
                                  </div>
                              </div>`)

    const data = {
        username: username,
        name: name,
        profile: profileSrc
    }

    $("#__btn__accept__req").click(() => {
        data.type = 'accept';
        $(this).prop('disabled', true)
        $("#__btn__reject__req").prop('disabled', true)
        if (modifyRequest(data)) {
            requests[i] = undefined;
            reqNode.remove();
        }
    })

    $("#__btn__reject__req").click(() => {
        data.type = 'reject';
        $(this).prop('disabled', true)
        $("#__btn__accept__req").prop('disabled', true)
        if (modifyRequest(data)) {
            requests[i] = undefined
            reqNode.remove();
        }
    })
}