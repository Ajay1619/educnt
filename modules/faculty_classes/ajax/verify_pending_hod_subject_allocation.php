<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $location_href = isset($_POST['location']) ? sanitizeInput($_POST['location'], 'string') : '';
        $verifying_year_of_study_id = isset($_POST['verifying_year_of_study_id']) ? sanitizeInput($_POST['verifying_year_of_study_id'], 'int') : '';
        $verifying_section_id = isset($_POST['verifying_section_id']) ? sanitizeInput($_POST['verifying_section_id'], 'int') : '';

        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
            ['name' => 'verifying_year_of_study_id', 'type' => 'i', 'value' => $verifying_year_of_study_id],
            ['name' => 'verifying_section_id', 'type' => 'i', 'value' => $verifying_section_id],
        ];

        $result = callProcedure("update_pr_verify_pending_hod_subject_allocation", $params_procedures);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
            exit;
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, $error_side);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
