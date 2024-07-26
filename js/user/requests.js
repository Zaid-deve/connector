function toggleAcceptPopup(e, username, name, profile, req_time, element, index) {
    showAcceptPopup(username, name, profile, req_time, element, index);
}

$(function () {

    $.get(`${ORIGIN}/php/fetchRequests.php`, function (resp) {
        const r = JSON.parse(resp),
            users = r.Users;

        if (users.length) {
            users.forEach((u, i) => {
                $(".requests-list").append(
                    `<tr class='req_te'>
                        <td>${i}</td>
                        <td><img src='${u.profile}' class='img-cover rounded-circle mb-2' height='40px' width='40px'>${u.username}</td>
                        <td>${u.req_time}</td>
                        <td>
                            <button class='btn text-primary border-0' onclick="toggleAcceptPopup(event, '${u.username}', '${u.name}', '${u.profile}', '${u.req_time}', this.closest('.req_te'), '${i}')">show</button>
                        </td>
                    </tr>`
                );
            });
            $(".req-fetch-status").text('all requests fetched !');
        } else {
            $(".req-fetch-status").text(r.Err);
        }
    });
});
