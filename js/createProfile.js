$(function () {
    // is profile added
    try {
        if (isProfileAdded) {
            return;
        }
    } catch { }


    showPopup('popup-set-profile');
    const username = $("#__username"),
        name = $("#__name"),
        usernameErr = $(".__username__err"),
        nameErr = $(".__name__err"),
        profileErr = $(".__user__profile__err"),
        submitBtn = $("#__btn_create_profile")

    function checkProfile(file) {
        if (file && file.size) {
            profileErr.text('');

            let fname = file.name,
                type = fname.split('.').pop(),
                formats = ['png', 'jpg', 'jpeg', 'webp', 'gif']

            if (!formats.includes(type)) {
                profileErr.text('Invalid Profile Image, Profile Image Should Be: ' + formats.join(','));
                return;
            }

            // preview file
            let blob = URL.createObjectURL(file);
            $(".__preview__imgsrc").prop('src', blob);
            // URL.revokeObjectURL(blob);
        }
    }

    function checkUsername(val, throwErr = true) {
        const isValid = /^(?![._])(?!.*\.\.)[a-zA-Z0-9._]{6,16}$/.test(val);

        if (throwErr) {
            if (isValid) {
                usernameErr.text("")
            } else {
                usernameErr.addClass('text-danger').text("username is not valid");
            }
        }

        return isValid
    }

    function checkName(val, throwErr = true) {
        const isValid = /^[a-zA-Z0-9\s]{0,24}$/.test(val)

        if (throwErr) {
            if (isValid) {
                nameErr.text("")
            } else {
                nameErr.addClass('text-danger').text("name is not valid");
            }
        }

        return isValid
    }

    function createProfile() {
        let data = new FormData();
        data.append('username', username.val())
        data.append('name', name.val())
        data.append('profile', $("#__user__profile__inp")[0].files[0])

        $.ajax({
            url: '../php/handleCreateProfile.php',
            type: 'POST',
            data,
            contentType: false,
            processData: false,
            success: function (resp) {
                const r = JSON.parse(resp)

                if (r.Success) {
                    location.reload();
                    return;
                }

                if (r.Err == "LOGIN_FAILED") {
                    location.replace('app/user/signin.php');
                    return;
                }

                if (r.NameErr) {
                    nameErr.text(r.NameErr).addClass('text-danger');
                }

                if (r.IdErr) {
                    usernameErr.text(r.IdErr).addClass('text-danger');
                }

                if (r.ProfileErr) {
                    profileErr.text(r.ProfileErr).addClass('text-danger')
                }

                if (r.Err) {
                    throwErr(r.Err)
                }

                submitBtn.prop('disabled', false);
                username.prop('readonly', false);
                name.prop('readonly', false);
                $("#__user__profile__inp").prop('disabled', false)
                throwErr(resp.Err)
            }
        })

    }

    $("#__user__profile__inp").on('change', function (e) {
        if (!e.target.files.length) {
            return;
        }
        checkProfile(e.target.files[0])
    })

    username.on('input', function () {
        if (checkUsername(username.val()) && checkName(name.val(), false)) {
            submitBtn.prop('disabled', false)
        } else {
            submitBtn.prop('disabled', true)
        }
    })

    name.on('input', function () {
        if (checkUsername(username.val(), false) && checkName(name.val())) {
            submitBtn.prop('disabled', false)
        } else {
            submitBtn.prop('disabled', true)
        }
    })

    submitBtn.click(function () {
        if (checkUsername(username.val()) && checkName(name.val()) && !profileErr.text()) {
            $(this).prop('disabled', true);
            username.prop('readonly', true);
            name.prop('readonly', true);
            $("#__user__profile__inp").prop('disabled', true)
            createProfile();
        } else {
            $(this).prop('disabled', false);
        }
    })
})