<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $route = isset($_POST['route']) ? sanitizeInput($_POST['route'], 'string') : 'Faculty';

        $_SESSION['routing'] = $route;

        echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Route changed successfully.', 'data' => $route]);
    } catch (PDOException $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
