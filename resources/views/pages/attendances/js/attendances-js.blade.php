<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const photoData = document.getElementById('photo-data');
    const captureButton = document.getElementById('capture');
    const openModalButton = document.getElementById('open-modal');
    let stream;

    // Open modal and reset photo data
    openModalButton.addEventListener('click', () => {
        // Access the device camera and stream to video element
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(mediaStream => {
                stream = mediaStream;
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing the camera: " + err);
            });

        photoData.value = ''; // Clear previous photo data
        $('#photoModal').modal('show');
    });

    // Capture the image from the video feed
    captureButton.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        // Get the image data and store it in a hidden input
        photoData.value = canvas.toDataURL('image/png');
        $('#photoModal').modal('hide');

        // Automatically submit the form after capturing the photo
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('/attendances/check_in') }}"; // Change this to the appropriate action
        form.innerHTML = `
            @csrf
            <input type="hidden" name="{{ $attendance == null ? 'clock_in' : 'clock_out' }}" value="{{ $attendance == null ? 'clock_in' : 'clock_out' }}">
            <input type="hidden" name="photo" value="${photoData.value}">
        `;
        document.body.appendChild(form);
        form.submit();
    });

    // Stop the video stream when the modal is closed
    $('#photoModal').on('hidden.bs.modal', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
