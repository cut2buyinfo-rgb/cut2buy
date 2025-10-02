<?php
// controllers/admin/AdminQnaController.php

// Security Check
if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// --- Handle POST Requests (Answering / Deleting) ---
if ($method === 'POST') {
    switch ($path) {
        case '/admin/qna/answer':
            $qna_id = $_POST['qna_id'];
            $answer = trim($_POST['answer'] ?? '');

            if ($qna_id && !empty($answer)) {
                try {
                    $stmt = $pdo->prepare(
                        "UPDATE product_qna 
                         SET answer = ?, answered_by = ?, answered_at = NOW() 
                         WHERE id = ?"
                    );
                    $stmt->execute([$answer, $user['id'], $qna_id]);
                } catch (PDOException $e) {
                    error_log("Failed to save answer: " . $e->getMessage());
                }
            }
            header('Location: /admin/qna');
            exit();

        case '/admin/qna/delete':
            $qna_id = $_POST['qna_id'];
            if ($qna_id) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM product_qna WHERE id = ?");
                    $stmt->execute([$qna_id]);
                } catch (PDOException $e) {
                    error_log("Failed to delete question: " . $e->getMessage());
                }
            }
            header('Location: /admin/qna');
            exit();
    }
}


// --- Default GET Request: Show Q&A List ---
$pageTitle = "Manage Q&A";
try {
    // Fetch all questions, joining with product and user tables to get names
    $qna_items = $pdo->query("
        SELECT 
            q.id, q.question, q.answer, q.created_at, 
            p.name as product_name, p.slug as product_slug,
            u.name as asker_name
        FROM product_qna q
        JOIN products p ON q.product_id = p.id
        JOIN users u ON q.user_id = u.id
        ORDER BY q.answer IS NULL DESC, q.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    require_once ROOT_PATH . '/views/admin/qna/index.view.php';

} catch (PDOException $e) {
    die("Error fetching Q&A: " . $e->getMessage());
}