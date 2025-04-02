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

        $r_r_id = isset($_POST['r_r_id']) ? sanitizeInput($_POST['r_r_id'], 'int')  : '';

        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept', 'type' => 'i', 'value' => 0],
            ['name' => 'committee id', 'type' => 's', 'value' => json_encode([])],
            ['name' => 'student id ', 'type' => 's', 'value' => json_encode([])],
            ['name' => 'committee role', 'type' => 's', 'value' => json_encode([])],
            ['name' => 'type', 'type' => 'i', 'value' => 1],
            ['name' => 'r_r_id', 'type' => 'i', 'value' => $r_r_id]
        ];
        $result = callProcedure("update_pr_student_dept_committee_roles", $procedure_params);

        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating personal details.']);
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
