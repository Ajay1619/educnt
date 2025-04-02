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

        $student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id'], 'int')  : [];
        $student_representative_id = isset($_POST['student_representative_id']) ? sanitizeInput($_POST['student_representative_id'], 'int')  : [];
        $rep_year_of_study_id = isset($_POST['rep_year_of_study_id']) ? sanitizeInput($_POST['rep_year_of_study_id'], 'int')  : [];
        $rep_section_id = isset($_POST['rep_section_id']) ? sanitizeInput($_POST['rep_section_id'], 'int')  : [];
        $rep_dept_id = isset($_POST['rep_dept_id']) ? sanitizeInput($_POST['rep_dept_id'], 'int')  : [];

        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'student_id', 'type' => 'i', 'value' => json_encode($student_id)],
            ['name' => 'student_representative_id', 'type' => 'i', 'value' => json_encode($student_representative_id)],
            ['name' => 'rep_year_of_study_id', 'type' => 'i', 'value' => json_encode($rep_year_of_study_id)],
            ['name' => 'rep_dept_id', 'type' => 'i', 'value' => json_encode($rep_dept_id)],
            ['name' => 'rep_section_id', 'type' => 'i', 'value' => json_encode($rep_section_id)]
        ];
        $result = callProcedure("update_pr_student_representative_form", $procedure_params);

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
