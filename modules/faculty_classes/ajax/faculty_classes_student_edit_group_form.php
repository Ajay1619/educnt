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
        $sem_duration_id = isset($_POST['sem_duration_id']) ? sanitizeInput($_POST['sem_duration_id'], 'int') : '';
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : '';
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : '';
        $academic_year_id = isset($_POST['academic_year_id']) ? sanitizeInput($_POST['academic_year_id'], 'int') : '';
        $sem_id = isset($_POST['sem_id']) ? sanitizeInput($_POST['sem_id'], 'int') : '';



        $student_group_chips_id = isset($_POST['student_group_chips_id']) ? sanitizeInput($_POST['student_group_chips_id'], 'int')  : [];
        $student_group_chips_value = isset($_POST['student_group_chips_value']) ? sanitizeInput($_POST['student_group_chips_value'], 'string') : [];

        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
            ['name' => 'academic_year_id', 'type' => 'i', 'value' => $academic_year_id],
            ['name' => 'year_of_study_id', 'type' => 'i', 'value' => $year_of_study_id],
            ['name' => 'sem_id', 'type' => 'i', 'value' => $sem_id],
            ['name' => 'section_id', 'type' => 'i', 'value' => $section_id],
            ['name' => 'sem_duration_id', 'type' => 'i', 'value' => $sem_duration_id],
            ['name' => 'student_group_chips_id', 'type' => 's', 'value' => json_encode($student_group_chips_id)],
            ['name' => 'student_group_chips_value', 'type' => 's', 'value' => json_encode($student_group_chips_value)],
        ];
        $result = callProcedure("update_pr_class_advisor_student_group", $params_procedures);
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
