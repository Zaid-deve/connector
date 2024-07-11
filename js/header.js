$(function () {
    $('.btn-toggle-requests').click(function () {
        if ($(".requests-menu-container").length) {
            $(".requests-menu-container").toggleClass('show')
            if ($(".requests-menu-container").hasClass('show')) {
                fetchRequests();
            }
        }
    })

    // fetch requests
    function fetchRequests() {
        $.post('../php/fetchRequests.php', function (resp) {
            const r = JSON.parse(resp);
            if (r.Success) {
                let output = "<div class='text-center text-muted fw-ligth py-3'>No Friend Requests</div>";
                if (r.Users.length) {
                    output = "<ul class='list-group requests-list'>";
                    r.Users.forEach(u => {
                        let profileSrc = '../images/main-qimg-6d72b77c81c9841bd98fc806d702e859-lq.jfif';
                        if (u.profile) {
                            profileSrc = '../' + u.profile;
                        }
                        output += `<li class="list-group-item border-0 rounded-0 request-item py-3" onclick="showAcceptPopup('${u.username}','${u.name}','${profileSrc}','${u.req_time}',this)">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="${profileSrc}" alt="#" class="rounded-circle img-cover bg-secondary flex-shrink-0" height="40" width="40">
                                            <div class="requester-info flex-shrink-0">
                                                <div class="fw-bold">@${u.username}</div>
                                                <small class="text-muted">${u.name}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <small class="text-muted">${u.req_time}</small>
                                            </div>
                                        </div>
                                    </li>`
                    });
                    output += '</ul>';
                }

                $(".requests-output").html(output);
            } else {
                if (r.Err) {
                    $(".requests-output").html(`<div class='text-center text-danger fw-ligth py-3'>${r.Err || 'Something Went Wrong !'}</div>`);
                }
            }
        })
    }

})

function showAcceptPopup(username, name, profileSrc, req_time, reqNode) {
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
        modifyRequest(data,reqNode);
    })

    $("#__btn__reject__req").click(() => {
        data.type = 'reject';
        $(this).prop('disabled', true)
        $("#__btn__accept__req").prop('disabled', true)
        modifyRequest(data,reqNode);
    })
}