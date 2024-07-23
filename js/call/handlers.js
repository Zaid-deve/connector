/*

============================
RTC HANDLERS
============================

*/


// HANDLER REMOTE TRACK

function trackHandler(e) {
    let stream = null;
    if (e.streams && e.streams[0]) {
        stream = e.streams[0];
    } else {
        stream = new MediaStream();
        stream.addTrack(e.track);
    }
    return stream;
}

// ICE CONNECTION HANDLER

function handleIceConnectionState(e) {
    const state = e.target.iceConnectionState;
    if (state == 'failed') {
        e.target.restartIce();
    }
}

// HANDLE CONNECTION STATES

function handleConnectionState(e) {
    let state = e.target.connectionState,
        style = 'muted';

    if (state === 'connected') {
        initRecorder(localStream, remoteStream, isVideoCall, recorderCallback);
        isRecording = true;
        callStatus = 'connected';
        style = 'success';
        showCallTime();
    } else if (state == 'disconnected' || state == 'failed') {
        callStatus = null;
        style = 'danger'
        state = 'ended';
    } else {
        state = 'connecting...';
    }

    if ($(".call-status").length) {
        $(".call-status").html(`<span class='text-${style}'>${state}</span>`);
    }
}