$(function () {
    const username = $("#__username"),
        usernameErr = username.next(),
        name = $("#__name"),
        nameErr = name.next(),
        profile = $("#__user__profile__inp"),
        profileErr = $(".profile-err"),
        submit_btn = $("#modifyProfileBtn");

    let isUsernameValid = isNameValid = isProfileValid = true,
        profileImgSrc = $(".__preview__imgsrc")[0].src;

    profile.on('change', function () {
        profileErr.text('');
        const file = profile[0].files[0];
        if (file && file.name) {
            const formats = ['png', 'jpeg', 'jpg', 'webp'];
            if (formats.includes(file.name.split('.').pop())) {
                const blob = URL.createObjectURL(file);
                $(".__preview__imgsrc")[0].src = blob;
            } else {
                $(".__preview__imgsrc")[0].src = profileImgSrc
                profileErr.text('Invalid profile image, please use ' + formats.join(','));
            }
        } else {
            $(".__preview__imgsrc")[0].src = profileImgSrc
            profileErr.text('Invalid profile image');
        }

        verifyFieldChange();
    })

    username.on('input', function () {
        usernameErr.text('');
        isUsernameValid = username.val().match(/^(?![._])(?!.*\.\.)[a-zA-Z0-9._]{6,16}$/)
        if (!isUsernameValid) {
            usernameErr.text('Username is not valid !');
        }

        verifyFieldChange();
    })

    name.on('input', function () {
        nameErr.text('');
        isNameValid = name.val().match(/^[a-zA-Z0-9\s]{0,24}$/)
        if (!isNameValid) {
            nameErr.text('Name is not valid !');
        }

        verifyFieldChange();
    })



    function verifyFieldChange() {
        if (isUsernameValid && isNameValid && isProfileValid) {
            submit_btn.removeClass('d-none')
            return true;
        } else {
            submit_btn.addClass('d-none')
        }
    }

    $('#profileEditForm').submit(function (e) {
        e.preventDefault()
        if (verifyFieldChange()) {
            usernameErr.text('')
            nameErr.text('')
            profileErr.text('')
            submit_btn[0].disabled = username[0].readonly = name[0].readonly = profile[0].disabled = true;

            const data = new FormData($('#profileEditForm')[0]);
            data.append('profile_img', profile[0].files[0] || '');

            $.ajax({
                url: `${ORIGIN}/user/updateProfile.php`,
                method: 'post',
                data: data,
                processData: false,
                contentType: false,
                success: function (resp) {
                    const r = JSON.parse(resp);
                    if (r.Success) {
                        location.replace(`${ORIGIN}/user/profile.php`);
                        return;
                    }

                    if (r.UsernameErr) {
                        usernameErr.text(r.UsernameErr);
                    }

                    if (r.NameErr) {
                        usernameErr.text(r.NameErr);
                    }

                    if (r.ProfileErr) {
                        usernameErr.text(r.ProfileErr);
                    }

                    if (r.Err) {
                        if (r.Err == 'LOGIN_ERR') {
                            location.replace('signin.php');
                            return;
                        }
                        throwErr(r.Err);
                    }
                },
                complete: function () {
                    submit_btn[0].disabled = username[0].readonly = name[0].readonly = profile[0].disabled = false;
                }
            });
        }
    })
})