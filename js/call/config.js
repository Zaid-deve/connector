const iceServers = [
    { urls: "stun:stun.l.google.com:19302" },
    { urls: "stun:stun1.l.google.com:19302" },
    { urls: "stun:stun2.l.google.com:19302" },
    { urls: "stun:stun3.l.google.com:19302" },
    { urls: "stun:stun4.l.google.com:19302" }
],
    callConfig = {
        from: userId,
        to: remoteUser,
    };

// DEFAULTS
let isCaller = false,
    callInitTime = null,
    callStatus = null,
    isCallEnded = false,
    isVideoCall = false;


// STREAMS
let localStream = null,
    remoteStream = null,
    isRecording = false,
    isMuted = false;


// CONNECTION
let peer = null;

// IS CALLER
isCaller = getParam('type') == 'answer' ? false : true;