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

        $faculty_id = decrypt_data($_POST['faculty_id']);
        //$faculty_id = isset($faculty_id) ? sanitizeInput($faculty_id, 'int') : 0;
        $faculty_status = isset($_POST['faculty_status']) ? sanitizeInput($_POST['faculty_status'], 'bool') : true;

        if ($faculty_status == true) {
            $faculty_status = 1;
        } else {
            $faculty_status = 0;
        }


        $procedure_params = [

            ['name' => 'faculty id', 'type' => 'i', 'value' => $faculty_id],
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_status', 'type' => 'i', 'value' => $faculty_status]

        ];
        $result = callProcedure("update_pr_faculty_status", $procedure_params);
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
