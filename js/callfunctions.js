// functions remoteUser show and manipulate calling status and connection

function displayCallStatus(status) {
    $(".call-status").html(status);
}

// init
let init;

function displayCallTime() {
    if (!pc || (pc && peer.connectionState != 'connected')) {
        return;
    }

    if (!init) {
        init = Date.now();
    } else {
        const diff = Date.now() - init;
        if (diff) {
            $('.call-time').text(formatTime(diff));
        }
    }

    setTimeout(displayCallTime, 1000);
}

// format time
function formatTime(milliseconds) {
    let seconds = Math.floor(milliseconds / 1000);
    let hours = Math.floor(seconds / 3600).toString();
    seconds = seconds % 3600;
    let minutes = Math.floor(seconds / 60).toString();
    seconds = (seconds % 60).toString();

    // Format the time string
    let diff = [];
    if (hours > 0) {
        diff.push(hours.padStart(2, '0'))
    }
    diff.push(minutes.padStart(2, '0'), seconds.padStart(2, '0'))

    return diff.join(':');
}

// remoteUserggle call end

function displayCallEnded() {
    $(".call-end-win").removeClass('d-none');
}

function sendHangupReq() {
    // send hangup req
    const data = {
        type: 'hangup',
        from: userId,
        to: remoteUser
    }
    wss.send(JSON.stringify(data))
    return true;
}

function handleCallReconnect() {
    displayCallStatus('re-connecting');
    peer.remoteDescription = null;
}

// Hangup call function
function hangupCall(type = null) {

    // call recording
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        stopRecording()
    }

    if (recordedChunks.length > 0) {
        $("#savebtn").removeClass('d-none');
    }

    // close connection
    if (pc) {
        displayCallStatus('ended');
        displayCallEnded();

        if (type == 'reject') {
            $(".call-header").text("Call Declined !");
            $('.call-msg').text('User is currently busy, please try again');
        }
        peer.getSenders().forEach(stream => {
            if (stream.track) {
                stream.track.stop();
            }
        })

        // null listeners
        peer.onicecandidate = null
        peer.ontrack = null
        peer.onconnectionstatechange = null
        peer.oniceconnectionstatechange = null

        // close
        peer.close();
        pc = null;
    }
}

function sendReconnectReq(from, to) {
    displayCallStatus('re-connecting');
    const data = {
        type: 'reconnect',
        from: from,
        to: to
    }
    wss.send(JSON.stringify(data))
    return true;
}

$(function () {
    // Hangup listener
    $(".btn-decline-call").click(function () {
        if (pc) {
            $(this).prop('disabled', true);
            // Hangup the call
            sendHangupReq();
            hangupCall();
        }
    });

    // Save call listener
    $("#savebtn").click(function () {
        saveRecording();
    });
});


/* 
=============================
audio call recording function 
============================= 
*/

// component
const
    recordedChunks = [],
    combinedStream = new MediaStream()

let
    mediaRecorder,
    audioContext;

function startRecording(localStream, remoteStream) {
    // create a media recorder
    audioContext = new AudioContext()
    mediaRecorder = new MediaRecorder(combinedStream)
    mediaRecorder.ondataavailable = function (e) {
        if (e.data.size) {
            recordedChunks.push(e.data);
        }
    }


    // combine source
    let localSource = audioContext.createMediaStreamSource(localStream),
        remoteSource = audioContext.createMediaStreamSource(remoteStream)
    const mergerNode = audioContext.createChannelMerger(2);

    localSource.connect(mergerNode)
    remoteSource.connect(mergerNode)

    const destination = audioContext.createMediaStreamDestination()
    mergerNode.connect(destination)

    combinedStream.addTrack(destination.stream.getAudioTracks()[0]);

    // start
    mediaRecorder.start();
}

function stopRecording() {
    if (mediaRecorder) {
        mediaRecorder.stop();
    }
}

function saveRecording() {
    let blob = new Blob(recordedChunks, { type: 'audio/mp3' }),
        url = URL.createObjectURL(blob),
        a = document.createElement('a')

    a.href = url;
    a.download = `connector_recording.mp3`;
    document.body.append(a);
    a.click();
    a.remove()
    URL.revokeObjectURL(url);
}