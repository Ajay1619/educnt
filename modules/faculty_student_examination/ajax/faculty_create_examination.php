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
    $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    
    try {   
        
        $exam_group_id = isset($_POST['exam_group_id']) ? sanitizeInput($_POST['exam_group_id'], 'int') : 0;
        $exam_type_id = isset($_POST['exam_type_id']) ? sanitizeInput($_POST['exam_type_id'], 'int') : 0;
        $exam_max_marks = isset($_POST['exam_max_marks']) ? sanitizeInput($_POST['exam_max_marks'], 'float') : 0;
        $exam_min_marks = isset($_POST['exam_min_marks']) ? sanitizeInput($_POST['exam_min_marks'], 'float') : 0;
        $exam_duration = isset($_POST['exam_duration']) ? sanitizeInput($_POST['exam_duration'], 'float') : 0;
        $examendDate = isset($_POST['examendDate']) ? sanitizeInput($_POST['examendDate'], 'string') : null;
        $examStartDate = isset($_POST['examStartDate']) ? sanitizeInput($_POST['examStartDate'], 'string') : null;
        $selected_dept_list = isset($_POST['selected_dept_list']) ? sanitizeInput($_POST['selected_dept_list'], 'string') : null;

        // $selected_dept_list = isset($_POST['selected_dept_list']) ? (array)$_POST['selected_dept_list'] : [];

        // Convert dates to MySQL format (YYYY-MM-DD)
        $exam_start_date = date(DB_DATE_FORMAT,strtotime($examStartDate)); 
        $exam_end_date = date(DB_DATE_FORMAT,strtotime($examendDate)); 
       


        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'exam_group_id', 'value' => $exam_group_id, 'type' => 'i'],
            ['name' => 'exam_type_id', 'value' => $exam_type_id, 'type' => 'i'],
            ['name' => 'exam_max_marks', 'value' => $exam_max_marks, 'type' => 'd'],
            ['name' => 'exam_min_marks', 'value' => $exam_min_marks, 'type' => 'd'],
            ['name' => 'exam_duration', 'value' => $exam_duration, 'type' => 'd'],
            ['name' => 'exam_start_date', 'value' => $exam_start_date, 'type' => 's'],
            ['name' => 'exam_end_date', 'value' => $exam_end_date, 'type' => 's'],
            // ['name' => 'exam_department_id', 'value' => $selected_dept_list, 'type' => 'i']
            ['name' => 'selected_dept_list', 'value' => json_encode($selected_dept_list), 'type' => 's']

        ];

         $result = callProcedure('insert_pr_Faculty_create_examination', $procedure_params); 
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                $data = $result['data']; 

                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message'],
                    'data' => $data
                ]);
                exit;
            } else {
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message']
                ]);
                exit;
            }
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        // insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => $error_message]);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>