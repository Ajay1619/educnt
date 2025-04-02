<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        if (isset($logged_login_id) && !empty($logged_login_id)) {
            $procedure_params = [
                ['name' => 'user id', 'value' => 0, 'type' => 'i'],
                ['name' => 'portal type', 'value' => $logged_portal_type, 'type' => 'i'],
                ['name' => 'log id', 'value' => $logged_login_id, 'type' => 'i'],
                ['name' => 'user IP address', 'value' => getUserIP(), 'type' => 's'],
                ['name' => 'successful login', 'value' => 1, 'type' => 'i'],
                ['name' => 'login status', 'value' => 1, 'type' => 'i'],
                ['name' => 'log out', 'value' => 0, 'type' => 'i']
            ];

            $result = callProcedure('login_validate', $procedure_params);
            if ($result) {
                if ($result['particulars'][0]['status_code'] == 200) {
                    session_destroy();
                    echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Logged out successfully.']);
                    exit;
                } else {
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Unable to Logout.']);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Unable to Logout.']);
            exit;
        }
    } catch (\Throwable $th) {
        //throw $th;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
