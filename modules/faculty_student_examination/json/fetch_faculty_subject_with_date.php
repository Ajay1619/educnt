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
        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
        $exam_id = isset($_POST['exam_id']) ? sanitizeInput($_POST['exam_id'], 'int') : 0;
        $sem_id = isset($_POST['sem_id']) ? sanitizeInput($_POST['sem_id'], 'int') : '';




        $params_procedures = [
             ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
            ['name' => 'year_of_study_id', 'type' => 'i', 'value' => $year_of_study_id],
            ['name' => 'sem_id', 'type' => 'i', 'value' => $sem_id],
            ['name' => 'faculty_id', 'type' => 'i', 'value' => $logged_user_id],
            ['name' => 'exam_id', 'type' => 'i', 'value' => $exam_id],
        ];
        
        $result = callProcedure("fetch_pr_subject_name_list_with_date", $params_procedures);
        // print_r($result);
       
        
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if ($result['data']) {
                    $year_of_study_data = $result['data']; 

                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $year_of_study_data, 'message' => $result['particulars'][0]['message']]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No Year Of Study Found For Selected Department.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
            exit;
        }
    } catch (\Throwable $th) {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
