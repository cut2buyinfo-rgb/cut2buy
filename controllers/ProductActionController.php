<?php
// File: controllers/ProductActionController.php
// Version: FINAL - Corrected syntax errors.


// Security: User must be logged in to perform these actions.
if (!$user) {
    header('Location: /login');
    exit();
}

// Get routing variables
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? null; // Get action from the fallback form
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// This controller only handles POST requests.
if ($method !== 'POST') {
    header('Location: /');
    exit();
}

// --- ACTION 1: HANDLE "ASK A QUESTION" ---
if ($path === '/product/ask-question' || $action === 'ask-question') {
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $question = trim($_POST['question'] ?? '');
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/';

    if (!$product_id || empty($question)) {
        $_SESSION['form_error'] = "Question cannot be empty.";
        // --- THIS LINE IS CORRECTED ---
        header('Location: ' . $redirect_url);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO product_qna (product_id, user_id, question) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $user['id'], $question]);
        $_SESSION['form_success'] = "Your question has been submitted successfully!";
    } catch (PDOException $e) {
        error_log("Failed to save question: " . $e->getMessage());
        $_SESSION['form_error'] = "Sorry, there was a technical issue. Please try again.";
    }
    
    // --- THIS LINE IS ALSO CORRECTED ---
    header('Location: ' . $redirect_url);
    exit();
}

// --- ACTION 2: HANDLE "SUBMIT REVIEW" ---
if ($path === '/product/submit-review' || $action === 'submit-review') {
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review_text = trim($_POST['review_text'] ?? '');
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/';

    if (!$product_id || !$rating || $rating < 1 || $rating > 5) {
        $_SESSION['form_error'] = "A star rating is required to submit a review.";
        header('Location: ' . $redirect_url);
        exit();
    }

    $review_image_path = null;
    $upload_dir = ROOT_PATH . '/assets/images/reviews/';
    if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0777, true); }
    
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === 0) {
        $file = $_FILES['review_image'];
        if (in_array($file['type'], ['image/jpeg', 'image/png'])) {
            $source_image = ($file['type'] === 'image/jpeg') ? @imagecreatefromjpeg($file['tmp_name']) : @imagecreatefrompng($file['tmp_name']);
            if ($source_image) {
                $filename = 'review-' . $user['id'] . '-' . uniqid() . '.webp';
                imagewebp($source_image, $upload_dir . $filename, 80);
                imagedestroy($source_image);
                $review_image_path = $filename;
            }
        }
    }

    try {
        $check_stmt = $pdo->prepare("SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ?");
        $check_stmt->execute([$user['id'], $product_id]);
        if ($check_stmt->fetch()) {
             $_SESSION['form_error'] = "You have already reviewed this product.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review_text, review_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $user['id'], $rating, $review_text, $review_image_path]);
            $_SESSION['form_success'] = "Thank you! Your review has been submitted.";
        }
    } catch (PDOException $e) {
        error_log("Failed to save review: " . $e->getMessage());
        $_SESSION['form_error'] = "Sorry, there was a technical issue submitting your review.";
    }

    header('Location: ' . $redirect_url);
    exit();
}

// If no known action or path is matched, redirect to the homepage.
header('Location: /');
exit();