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

    // Create a unique ID for the new alert
    const alertId = `alert-${Date.now()}`;

    // Prepend a new alert with the unique ID
    $(".page-alert-container").prepend(`
    <div id="${alertId}" class="alert page-alert bg-white d-flex align-items-center m-0">
        <i class="fa-solid fa-triangle-exclamation tdanger"></i>
        <span class="alert-text tdanger tnormal ps-2 pe-5">${msg || 'Something Went Wrong'}</span>
        <button class="btn p-0 alert-close border-0 ms-auto" onclick="this.closest('.alert').remove()">
            <i class="fa-solid fa-close tdanger"></i>
        </button>
    </div>
    `);

    if (!strict) {
        setTimeout(() => {
            $(`#${alertId}`)?.remove();
        }, 3000);
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

function getParam(param) {
    const params = new URLSearchParams(location.search);

    if (param) {

        return params.get(param)
    }

    return params;
}

async function hasPerm(name) {
    const permissionStatus = await navigator.permissions.query({ name });
    return permissionStatus.state === 'granted'
}

function removeParam(param) {
    const url = new URL(window.location),
        searchParams = url.searchParams;
    searchParams.delete(param);

    let uri = url.pathname + url.search
    history.replaceState(null, '', uri);
    return uri;
}