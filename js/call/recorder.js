// ==============================
// RECORDING FUNCTIONS
// =============================

let mediaRecorder,
    recordedChunks = [],
    combinedStream;

function initRecorder(localStream, remoteStream, isVideo, callback) {
    if (isVideo) {
        const canvas = document.createElement('canvas'),
            context = canvas.getContext("2d"),
            height = canvas.height = 768,
            width = canvas.width = 1440,
            videoWidth = width / 2;

        // draw
        function draw() {
            context.clearRect(0, 0, width, height)
            context.drawImage(localVideo, 0, 0, videoWidth, height)
            context.drawImage(remoteVideo, videoWidth, 0, videoWidth, height)
            requestAnimationFrame(draw)
        }
        draw();
        combinedStream = canvas.captureStream(30);
        startRecorder(combinedStream, callback)
        return;
    }

    combinedStream = new MediaStream()

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

function stopRecorder() {
    if (mediaRecorder) {
        mediaRecorder.stop();
    }
}

function prepareBlob(chunks, mime) {
    const blob = new Blob(chunks, { type: mime });
    return URL.createObjectURL(blob);
}