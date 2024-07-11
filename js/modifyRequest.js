function modifyRequest(reqInfo = {}, reqNode) {
    if (!reqInfo.username) return;

    $.post('../php/modifyRequest.php', { type:reqInfo.type, username: reqInfo.username }, function (resp) {
        const r = JSON.parse(resp);

        if (r.Success) {
            if (reqNode) reqNode.remove();
            closePopup('popup-accept-req');

            if (reqInfo.type == 'accept') {
                let output = `<li class='list-group-item border-0 rounded-0 friend-list-item py-3'>
                                  <div class='d-flex align-items-center gap-2'>
                                      <img src='${reqInfo.profile}' alt='#' class='rounded-circle img-cover flex-shrink-0 friend-profile-img' height='46' width='46'>
                                      <div class='friend-info flex-shrink-0'>
                                          <div class='fw-bold'>@${reqInfo.username}</div>
                                          <small class='text-muted fw-light'>${reqInfo.name}</small>
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
        }

        if (r.Err) {
            if (r.Err == 'LOGIN_FAILED') {
                location.replace('../user/signin.php');
                return;
            } else {
                throwErr(r.Err);
            }
        }
        $("#__btn__reject__req,#__btn__accept__req").prop('disabled', false)
    })
}