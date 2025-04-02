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

        $from_faculty_id = isset($_POST['from_faculty_id']) ? sanitizeInput($_POST['from_faculty_id'], 'int')  : 0;
        $to_faculty_id = isset($_POST['to_faculty_id']) ? sanitizeInput($_POST['to_faculty_id'], 'int')  : 0;
        $location_href = isset($_POST['location_href']) ? sanitizeInput($_POST['location_href'], 'string')  : '';


        $update_mentor_procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'update_type', 'type' => 'i', 'value' => 2],
            ['name' => 'mentor_details_json', 'type' => 's', 'value' => ""],
            ['name' => 'from_faculty_id', 'type' => 'i', 'value' => $from_faculty_id],
            ['name' => 'to_faculty_id', 'type' => 'i', 'value' => $to_faculty_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
        ];

        $update_mentor_result = callProcedure("update_pr_faculty_mentor_role", $update_mentor_procedure_params);

        if ($update_mentor_result) {
            if ($update_mentor_result['particulars'][0]['status_code'] == 200) {
                echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Students Swapped successfully.']);
                exit;
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in Swapping Mentor Details.']);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in Swapping Mentor Details.']);
            exit;
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
