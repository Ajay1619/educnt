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

        $faculty_designation = isset($_POST['faculty_designation']) ? sanitizeInput($_POST['faculty_designation'], 'int') : 0;
        $faculty_dept = isset($_POST['faculty_dept']) ? sanitizeInput($_POST['faculty_dept'], 'int') : 0;
        $faculty_joining_date = isset($_POST['joining_date']) ? sanitizeInput($_POST['joining_date'], 'string') : '';
        $faculty_salary = isset($_POST['faculty_salary']) ? sanitizeInput($_POST['faculty_salary'], 'float') : '';


        if ($faculty_designation == 0) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please Select Your Designation.']);
            exit;
        }
        if ($faculty_dept == 0) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please Select Your Department.']);
            exit;
        }
        if (empty($faculty_joining_date)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please Select Your Date of Joining.']);
            exit;
        }

        $procedure_params = [
            ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_in_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'faculty_designation', 'value' => $faculty_designation, 'type' => 'i'],
            ['name' => 'faculty_dept', 'value' => $faculty_dept, 'type' => 'i'],
            ['name' => 'faculty_salary', 'value' => $faculty_salary, 'type' => 's'],
            ['name' => 'faculty_joining_date', 'value' => date(DB_DATE_FORMAT, strtotime($faculty_joining_date)), 'type' => 's'],

        ];
        $result = callProcedure("update_pr_faculty_personal_official_profile_info", $procedure_params);
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating Official details.']);
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
