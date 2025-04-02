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
        $routing_link = isset($_POST['routing_link']) ? sanitizeInput($_POST['routing_link'], 'string') : '';

        if ($routing_link) {
            $page_title = '';
            foreach ($faculty_page_access_data as $key => $pages) {

                if ($pages['page_link'] == $_POST['routing_link']) {
                    $page_title = $pages['page_title'];
                    echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Page title fetched successfully.', 'data' => $page_title]);
                    exit;
                }
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid Routing request.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
