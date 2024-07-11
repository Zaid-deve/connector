$(function () {
    const emailField = $('#email'),
        passField = $('#pass'),
        emailErr = $('.email-err'),
        passErr = $('.pass-err'),
        submitButton = $('#submit')

    let emailChecked = false;

    function validateEmail() {
        const isValidEmail = isEmail(emailField.val());
        if (isValidEmail || !emailField.val()) {
            emailErr.text('');
        } else {
            emailErr.text('Please enter a valid email address.');
        }

        return isValidEmail;
    }

    function validatePassword() {
        const pass = passField.val(),
            isPassValid = pass.length >= 8 && pass.length <= 255

        if (isPassValid) {
            passErr.text('');
        } else {
            passErr.text('Password should be 8 to 255 characters long.');
        }

        return isPassValid;
    }

    emailField.on('input', function () {
        submitButton.prop('disabled', !validateEmail())
    });

    passField.on('input', function () {
        submitButton.prop('disabled', !validatePassword())
    });

    submitButton.click(function () {
        if (!emailChecked) {
            if (validateEmail()) {
                emailChecked = true
                $(".field-pass").removeClass('d-none')
                $(".field-email").addClass('d-none');
                pass.focus()
            }
        } else {
            if (validatePassword()) {
                submitButton.hide();
                let email = emailField.val().trim(),
                    pass = passField.val().trim();


                $.post("../php/handleSignin.php", { email, pass }, function (resp) {
                    const r = JSON.parse(resp);
                    if (r.Success) {
                        location.replace("../app/chat.php");
                        return
                    }

                    if (r.ErrType) {
                        if (r.ErrType == "Email") {
                            emailChecked = false
                            emailField.val('');
                            emailErr.text(r.Err)
                            $(".field-pass").addClass('d-none')
                            $(".field-email").removeClass('d-none');
                        } else {
                            passErr.text(r.Err);
                        }
                    } else {
                        throwErr(r.Err, true);
                    }
                })
            }
        }
    })
});
