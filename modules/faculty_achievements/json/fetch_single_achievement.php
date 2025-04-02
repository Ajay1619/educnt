<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and contains the CSRF token
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

    try {
        $achievement_id = isset($_POST['achievement_id']) ? sanitizeInput($_POST['achievement_id'], 'int') : 0;
        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'achievement_id', 'value' => $achievement_id, 'type' => 'i'],
        ];

        $result = callProcedure("fetch_pr_single_faculty_achievement", $procedure_params);
        if ($result && isset($result['particulars'][0])) {
            $status_code = $result['particulars'][0]['status_code'];
            if ($status_code == 200) {
                $data = $result['data'][0] ?? null;
                if ($data) {
                    echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Achievement fetched successfully', 'data' => $data]);
                } else {
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Achievement data not found']);
                }
            } else {
                echo json_encode(['code' => $status_code, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
            }
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Failed to fetch data']);
        }
    } catch (Exception $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
}
