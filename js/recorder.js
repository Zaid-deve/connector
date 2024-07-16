// ==============================
// RECORDING FUNCTIONS
// =============================

let mediaRecorder,
    recordedChunks = [],
    combinedStream = new MediaStream();

function initRecorder(localStream, remoteStream, callback) {

    if (localStream) {
        localStream.getTracks().forEach(track => combinedStream.addTrack(track))
    }

    if (remoteStream) {
        remoteStream.getTracks().forEach(track => combinedStream.addTrack(track))
    }

    if (combinedStream.getTracks().length) {
        startRecorder(combinedStream, callback);
    }

}

function startRecorder(stream, callback) {
    if (stream) {
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.ondataavailable = function (e) {
            if (e.data.size > 0) {
                recordedChunks.push(e.data);
            }
        }
        mediaRecorder.onstop = function () {
            if (recordedChunks.length) {
                let downloadUri = prepareBlob(recordedChunks, mediaRecorder.mimeType);
                if (typeof callback == 'function') {
                    callback(downloadUri)
                }
            }
        }
        mediaRecorder.start();
    }
}

function stopRecorder(){
    if(mediaRecorder){
        mediaRecorder.stop();
    }
}

function prepareBlob(chunks, mime = 'audio/webm') {
    const blob = new Blob(chunks, { type: mime });
    return URL.createObjectURL(blob);
}