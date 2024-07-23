function modifyRequest(reqInfo = {}) {
    if (!reqInfo.username) return;

    $.post('../php/modifyRequest.php', { type: reqInfo.type, username: reqInfo.username }, function (resp) {
        const r = JSON.parse(resp);

        if (r.Success) {
            closePopup('popup-accept-req');
            $("#__btn__reject__req,#__btn__accept__req").prop('disabled', false)

            if (reqInfo.type == 'accept') {
                let peer = reqInfo.peer,
                    name = reqInfo.name,
                    username = reqInfo.username,
                    profile = reqInfo.profile;

                let output = `<li class='list-group-item border-0 rounded-0 friend-list-item py-3' oncontextmenu='toggleContextMenu(event)' data-username='${peer}' data-isstar='0' data-isblocked='0' onclick=\"previewCallOptions('${peer}','${username}', '${name}', '${profile}')\">
                                  <div class='d-flex align-items-center gap-2'>
                                      <img src='${profile}' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                      <div class='friend-info flex-shrink-0'>
                                          <div class='fw-bold'>@${username}</div>
                                          <small class='text-muted fw-light'>${name}</small>
                                      </div>
                                      <div class='ms-auto d-none'>
                                          <small class='text-success'>online</small>
                                      </div>
                                  </div>
                              </li>`
                if ($(".friends-list-outer .friends-list").length) {
                    $(".friends-list-outer .friends-list").prepend(output);
                } else {
                    $(".friends-list-outer").html('<ul class="list-group friends-list"></ul>')
                    $(".friends-list-outer .friends-list").prepend(output);
                }
            }
            return true;
        }

        if (r.Err) {
            if (r.Err == 'LOGIN_FAILED') {
                location.replace('../user/signin.php');
                return;
            } else {
                throwErr(r.Err);
                return;
            }
        }
    })
    return true;
}