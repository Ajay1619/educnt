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

        $committee_id = isset($_POST['committee_id']) ? sanitizeInput($_POST['committee_id'], 'int')  : '';
        $faculty_id = isset($_POST['faculty_name']) ? sanitizeInput($_POST['faculty_name'], 'int')  : '';
        $commitee_roles = isset($_POST['commitee_roles']) ? sanitizeInput($_POST['commitee_roles'], 'int')  : '';

        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept id', 'type' => 'i', 'value' => $logged_dept_id],
            ['name' => 'committee_id', 'type' => 'i', 'value' => json_encode($committee_id)],
            ['name' => 'faculty_id', 'type' => 'i', 'value' => json_encode($faculty_id)],
            ['name' => 'commitee_roles', 'type' => 'i', 'value' => json_encode($commitee_roles)],
            ['name' => 'type', 'type' => 'i', 'value' => 0],
            ['name' => 'r_r_id', 'type' => 'i', 'value' => 0]
        ];
        $result = callProcedure("update_pr_faculty_dept_committee_roles", $procedure_params);

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
