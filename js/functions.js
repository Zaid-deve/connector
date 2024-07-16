const HOST = location.host,
    ORIGIN = location.origin + '/connector',
    SCHEME = location.protocol

const isEmail = (val) => {
    return /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(val);
}

const isPassValid = (val) => {
    return val.length > 8 && val.length < 255
}

const throwErr = (msg, strict = false) => {
    if (!msg) return;
    $(".page-alert").removeClass('d-none')
    $(".page-alert").find('.alert-text').text(msg)
    if (!strict) {
        setTimeout(() => {
            $(".page-alert").addClass('d-none')
        }, 4000);
    }
}

function openhomenav() {
    const left = $(".home-row-left"),
        right = $(".home-row-right")

    left[0].classList.toggle('d-none');
    right[0].classList.toggle('d-none');
}

function formatTime(ms) {
    const totalSeconds = Math.floor(ms / 1000),
        hours = Math.floor(totalSeconds / 3600),
        minutes = Math.floor((totalSeconds % 3600) / 60),
        seconds = totalSeconds % 60,
        formattedHours = hours > 0 ? String(hours).padStart(2, '0') : '',
        formattedMinutes = String(minutes).padStart(2, '0'),
        formattedSeconds = String(seconds).padStart(2, '0');

    if (formattedHours) {
        return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
    }
    return `${formattedMinutes}:${formattedSeconds}`;
}