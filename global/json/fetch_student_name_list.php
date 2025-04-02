<?php
include_once('../../config/sparrow.php');

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
        $dept_id = isset($_POST['faculty_dept_id']) ? sanitizeInput($_POST['faculty_dept_id'], 'int') : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
        $group_id = isset($_POST['group_id']) ? sanitizeInput($_POST['group_id'], 'int') : 0;
        $procedure_params = [
            ['name' => 'dept_id', 'value' => $dept_id, 'type' => 'i'],
            ['name' => 'year_of_study_id', 'value' => $year_of_study_id, 'type' => 'i'],
            ['name' => 'section_id', 'value' => $section_id, 'type' => 'i'],
            ['name' => 'group_id', 'value' => $group_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];

        $result = callProcedure("fetch_pr_student_name_list", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0])) {
                    $data = $result['data'][0];
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data]);
                    exit;
                } else {
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No Student data found with your Filter.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        }
    } catch (\Throwable $th) {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error Occured : ' . $th->getMessage()]);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
