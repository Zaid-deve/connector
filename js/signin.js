$(function () {
    const email = $("#__email"),
        pass = $("#__pass"),
        emailErr = email.next(),
        passErr = pass.next(),
        submitBtn = $("#submit");

    let isEmailVerified = false;
    function isPassValid(val) {
        return val.length > 8 && val.length < 55;
    }

    email.on("input", function () {
        submitBtn[0].disabled = !isEmail(email.val());
    })

    pass.on("input", function () {
        submitBtn[0].disabled = !isPassValid(pass.val())
    })

    $("#signinform").on("submit", function (e) {
        e.preventDefault()
        emailErr.text('')
        passErr.text('')

        const emailValid = isEmail(email.val()),
            passValid = isPassValid(pass.val());

        if (!isEmailVerified) {
            if (emailValid) {
                isEmailVerified = true;
                email.parent().addClass('d-none')
                pass.parent().removeClass('d-none').hide().fadeIn(350);
                $(".btn-text").text('Sign In').fadeIn(350)
            } else {
                emailErr.text('Please enter a valid email address');
            }
            return;
        } else {
            if (!passValid) {
                passErr.text('Password Is Not Valid')
                return;
            }
        }

        sendSignReq(`${ORIGIN}/php/handleSignin.php`, { email: email.val(), pass: pass.val() });
    })

    function sendSignReq(authUrl, data) {
        $(".field .form-control")[0].readonly = false
        submitBtn.disabled = false;

        $.ajax({
            url: authUrl,
            method: 'POST',
            data: data,
            success: function (resp) {
                const r = JSON.parse(resp);
                if (r.Success) {
                    location.replace(`${ORIGIN}/app/chat.php`);
                }

                if (r.Err) {
                    if (r.ErrType == 'Email') {
                        emailErr.text(r.Err)
                    }
                    else if (r.ErrType == 'Password') {
                        passErr.text(r.Err)
                    } else {
                        throwErr(r.Err)
                    }
                } else {
                    throwErr("Something Went Wrong !");
                }
            },
            error: function (status) {
                throwErr(status);
            },
            complete: function () {
                $(".field .form-control")[0].readonly = false
                submitBtn.disabled = false;
            }
        })
    }
})