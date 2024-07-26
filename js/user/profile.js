$(function () {
    const profile_options = $(".profile-options");
    profile_options.children().each((i, e) => {
        $(e).click(function () {
            let d = $(this).data('opt')
            if (d) {
                if (d == 'logout') {
                    location.replace('logout.php')
                    return;
                }

                location.href = `${ORIGIN}/user/profile.php?opt=${d}`
            }
        })
    })
})