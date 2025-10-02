<?php
// File: /ajax_image_uploader.php
// This script handles real-time image uploads via AJAX.

// Start session to verify the user is an admin
session_start();

// --- Security Check: Ensure only logged-in admins can use this endpoint ---
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permission denied.']);
    exit();
}

// Function to convert and save the image
function convertImageToWebp($file_data, $destination_folder, $quality = 75) {
    if (!isset($file_data['tmp_name']) || !is_uploaded_file($file_data['tmp_name'])) { return false; }
    $source_path = $file_data['tmp_name'];
    $image_info = getimagesize($source_path);
    if ($image_info === false) return false;
    $mime = $image_info['mime'];
    switch ($mime) {
        case 'image/jpeg': $image = imagecreatefromjpeg($source_path); break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            imagepalettetotruecolor($image); imagealphablending($image, true); imagesavealpha($image, true);
            break;
        case 'image/gif': $image = imagecreatefromgif($source_path); break;
        default: return false;
    }
    if (!$image) return false;
    $new_filename = uniqid('img_', true) . '.webp'; // More unique filename
    $destination_path = $destination_folder . $new_filename;
    if (imagewebp($image, $destination_path, $quality)) {
        imagedestroy($image);
        return $new_filename;
    }
    imagedestroy($image);
    return false;
}

// --- Main Upload Logic ---
header('Content-Type: application/json');

if (isset($_FILES['images'])) {
    $upload_dir = __DIR__ . '/assets/images/products/';
    if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
        echo json_encode(['success' => false, 'error' => 'Server error: Upload directory is not writable.']);
        exit();
    }

    $uploaded_files = [];
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $single_file_data = [
                'name' => $_FILES['images']['name'][$key],
                'tmp_name' => $tmp_name
            ];
            $new_filename = convertImageToWebp($single_file_data, $upload_dir);
            if ($new_filename) {
                $uploaded_files[] = [
                    'filename' => $new_filename,
                    'url' => '/assets/images/products/' . $new_filename
                ];
            }
        }
    }

    if (!empty($uploaded_files)) {
        echo json_encode(['success' => true, 'files' => $uploaded_files]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Upload failed. Please check file type and size.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No files were uploaded.']);
}