<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {


    try {
        $error_message = isset($_POST['error_message']) ? sanitizeInput($_POST['error_message'], 'string') : '';
        $location_href = isset($_POST['location_href']) ? sanitizeInput($_POST['location_href'], 'string') : '';
        $error_side = isset($_POST['error_side']) ? sanitizeInput($_POST['error_side'], 'int') : 1;
        insert_error($error_message, $location_href, $error_side);

        echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Route changed successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
