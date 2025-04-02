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

        $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int')  : [];
        $faculty_class_advisors_id = isset($_POST['faculty_class_advisors_id']) ? sanitizeInput($_POST['faculty_class_advisors_id'], 'int')  : [];
        $ca_year_of_study_id = isset($_POST['ca_year_of_study_id']) ? sanitizeInput($_POST['ca_year_of_study_id'], 'int')  : [];
        $ca_section_id = isset($_POST['ca_section_id']) ? sanitizeInput($_POST['ca_section_id'], 'int')  : [];
        $faculty_dept_id = isset($_POST['faculty_dept_id']) ? sanitizeInput($_POST['faculty_dept_id'], 'int')  : [];

        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_id', 'type' => 'i', 'value' => json_encode($faculty_id)],
            ['name' => 'faculty_class_advisors_id', 'type' => 'i', 'value' => json_encode($faculty_class_advisors_id)],
            ['name' => 'ca_year_of_study_id', 'type' => 'i', 'value' => json_encode($ca_year_of_study_id)],
            ['name' => 'ca_section_id', 'type' => 'i', 'value' => json_encode($ca_section_id)],
            ['name' => 'faculty_dept_id', 'type' => 'i', 'value' => json_encode($faculty_dept_id)]
        ];
        $result = callProcedure("update_pr_faculty_dept_class_advisors_roles", $procedure_params);
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
