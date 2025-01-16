<!DOCTYPE html>
<html>
<head>
    <title>Coordinate Import Progress</title>
</head>
<body>
    <h1>Coordinate Import Progress</h1>
    <div id="progress-bar" style="width:0%; background-color:#4CAF50; color:white; padding:10px; text-align:center;">
        0%
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      const progressInterval = setInterval(() => {
    fetch('/import-progress')
        .then(response => response.json())
        .then(data => {
            if (data.progress !== undefined && data.message !== undefined) {
                updateProgress(data.progress);
                console.log(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching progress:', error);
        });
}, 5000); // Check every 5 seconds

function updateProgress(progress) {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = `${progress}%`;
    progressBar.textContent = `${progress}%`;
}

// Stop checking after the process is done
fetch('/import-coordinates')
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        alert('Import completed!');
    })
    .catch(error => {
        console.error('Error importing coordinates:', error);
    });

    </script>
</body>
</html>
