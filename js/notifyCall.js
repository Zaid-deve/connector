$(function () {
    if (wss && userId) {
        let timeLeft = null,
            ring = null,
            callStatus = null;

        // load incomming call audio
        function createRing() {
            const ring = new Howl({
                src: ['../audios/incomming.mp3'],
                loop: true,
                volume: 0.5
            });
            return ring;
        }

        function handleCallEnded() {
            callStatus = 'ended';
            if (ring) {
                ring.pause();
                ring = null
            }
            $('.inc-profile').attr('src', '#');
            $('.inc-username').text('')
            $('.inc-name').text('')
            $('.inc-call-msg').text('')
            $('.popup-inc-call').addClass('popup-dismisable');
            closePopup('popup-inc-call')
            $('.popup-inc-call').removeClass('popup-dismisable');
        }


        function handleTimeLeft(expiry) {
            if (callStatus != 'ringing') return;
            const current = Math.floor(Date.now() / 1000),
                diff = expiry - current,
                timeLeft = Math.floor(diff);

            $('.inc-time-left').text(`(${timeLeft}s)`)
            if (timeLeft > 0) {
                setTimeout(() => {
                    handleTimeLeft(expiry);
                }, 1000);
            } else {
                handleCallEnded()
            }
        }

        function handleRejectCall(from) {
            const data = {
                type: 'reject',
                from: from,
                to: userId
            }
            wss.send(JSON.stringify(data));
            handleCallEnded();
        }

        function getUserData(username, callback) {
            if (!username) return;
            $.post(`${ORIGIN}/php/getUserInfo.php`, { username }, function (resp) {
                const r = JSON.parse(resp);
                callback(r.Data || [], r.Success ? null : r.Err);
            }).fail(() => callback(null, 'Something Went Wrong'));
        }

        wss.onmessage = function (e) {
            const data = JSON.parse(e.data);

            if (data.type == 'hangup' && data.to == userId) {
                if (callStatus == 'ringing') {
                    handleCallEnded()
                }
                return;
            }

            if (data.type == "incomming" && data.to == userId) {
                // defaults
                let from = data.from,
                    callType = data.callType,
                    callExpires = parseInt(data.expires),
                    callUri = `${ORIGIN}/app/call/voice.php?userId=${from}&type=answer`

                getUserData(from, function (userData, err) {
                    callStatus = 'ringing';
                    ring = createRing();
                    if (ring) ring.play()
                    showPopup('popup-inc-call');
                
                    // show user info
                    if (userData.username) {
                        $('.inc-profile').attr('src', userData.profile);
                        $('.inc-username').text(userData.username)
                        $('.inc-name').text(userData.name)
                        $('.inc-call-msg').text(`incomming ${callType} call from ${userData.name || userData.username}`)
                    } else {
                        $('.inc-call-msg').text(err);
                    }

                    handleTimeLeft(callExpires / 1000);


                    if (callType === 'video') {
                        $(".inc-icon").html('<i class="ri-video-line"></i>')
                        callUri = `${ORIGIN}/app/call/video.php?userId=${from}&type=answer`
                    }

                    $("#__btn__accept__call").click(function () {
                        location.href = callUri
                    })

                    $("#__btn__reject__call").click(function () {
                        handleRejectCall(from);
                    })
                });

            }
        }
    }
})