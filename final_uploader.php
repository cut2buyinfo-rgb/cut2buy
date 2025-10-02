<?php
// final_uploader.php - A single, self-contained script for large, resumable file uploads.
// It does NOT require any external libraries like flow-php-server.

// --- PART 1: PHP BACKEND LOGIC (Handles the chunks) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['flowIdentifier'])) {
        $tempDir = __DIR__ . '/chunks_temp'; // A directory to store temporary chunks.
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        $finalDir = __DIR__; // The directory where the final file will be saved.

        $chunkNumber = isset($_GET['flowChunkNumber']) ? intval($_GET['flowChunkNumber']) : 0;
        $totalChunks = isset($_GET['flowTotalChunks']) ? intval($_GET['flowTotalChunks']) : 0;
        $identifier = preg_replace('/[^0-9a-zA-Z_-]/', '', $_GET['flowIdentifier']);
        $filename = $_GET['flowFilename'];
        $chunkFile = $tempDir . '/' . $identifier . '.part' . $chunkNumber;
        $finalFile = $finalDir . '/' . $filename;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (file_exists($chunkFile)) {
                header("HTTP/1.1 200 Ok");
            } else {
                header("HTTP/1.1 204 No Content");
            }
            exit;
        }

        if (!empty($_FILES)) {
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                move_uploaded_file($_FILES['file']['tmp_name'], $chunkFile);
                if ($chunkNumber == $totalChunks) {
                    $fp = fopen($finalFile, 'w+');
                    for ($i = 1; $i <= $totalChunks; $i++) {
                        $partFile = $tempDir . '/' . $identifier . '.part' . $i;
                        fwrite($fp, file_get_contents($partFile));
                        unlink($partFile);
                    }
                    fclose($fp);
                    rmdir($tempDir);
                }
                header("HTTP/1.1 200 Ok");
            } else {
                header("HTTP/1.1 500 Internal Server Error");
            }
            exit;
        }
    }
}
// --- END OF PHP BACKEND LOGIC ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Resumable Uploader</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f1f2f6; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        #drop-zone { border: 2px dashed #ccc; border-radius: 8px; padding: 50px; margin-bottom: 20px; cursor: pointer; transition: border-color 0.3s; }
        #drop-zone.hover { border-color: #0d6efd; background-color: #f8f9fa; }
        .progress-bar { width: 0; height: 24px; background-color: #0d6efd; text-align: center; line-height: 24px; color: white; border-radius: 5px; transition: width 0.3s; }
        .progress-container { width: 100%; background-color: #e9ecef; border-radius: 5px; display: none; }
        .status { margin-top: 15px; font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>

<div class="container">
    <h1>Final Large File Uploader</h1>
    <p>Upload your large `vendor.zip` file. It will be sent in small, safe chunks.</p>

    <div id="drop-zone">Drag & Drop your `vendor.zip` file here or click to select</div>
    <input type="file" id="file-input" style="display: none;">

    <div class="progress-container">
        <div class="progress-bar" id="progress-bar">0%</div>
    </div>
    <div class="status" id="status"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flow.js@2/dist/flow.min.js"></script>
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const progressBar = document.getElementById('progress-bar');
    const progressContainer = document.querySelector('.progress-container');
    const statusDiv = document.getElementById('status');

    const r = new Flow({
        target: 'final_uploader.php', // It will upload to this very same file.
        chunkSize: 1 * 1024 * 1024, // 1MB chunks - Safe for any hosting.
        testChunks: true
    });

    r.assignBrowse(fileInput, false);
    r.assignDrop(dropZone);

    r.on('fileAdded', function(file){
        progressContainer.style.display = 'block';
        statusDiv.textContent = 'Starting upload...';
        r.upload();
    });

    r.on('progress', function(){
        let progress = Math.floor(r.progress() * 100);
        progressBar.style.width = progress + '%';
        progressBar.textContent = progress + '%';
    });

    r.on('fileSuccess', function(file, message){
        statusDiv.textContent = '✅ Upload Complete! The file `'+ file.name +'` is now on your server.';
        progressBar.style.backgroundColor = '#28a745';
    });

    r.on('fileError', function(file, message){
        statusDiv.textContent = '❌ Upload Failed. Please check your connection and try again.';
        progressBar.style.backgroundColor = '#dc3545';
    });

    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('hover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('hover'));
    dropZone.addEventListener('drop', () => dropZone.classList.remove('hover'));
</script>

</body>
</html>