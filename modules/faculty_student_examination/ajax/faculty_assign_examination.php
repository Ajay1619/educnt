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
        $exam_date = isset($_POST['examDate']) ? sanitizeInput($_POST['examDate'], 'string') : null;
        $exam_start_time = isset($_POST['examStartTime']) ? sanitizeInput($_POST['examStartTime'], 'string') : null;
        $exam_end_time = isset($_POST['examEndTime']) ? sanitizeInput($_POST['examEndTime'], 'string') : null;
        $session_filter = isset($_POST['session_filter']) ? sanitizeInput($_POST['session_filter'], 'int') : 0; 
        $exam_id = isset($_POST['exam_id']) ? sanitizeInput($_POST['exam_id'], 'int') : 0;
        $year_id = isset($_POST['yid']) ? sanitizeInput($_POST['yid'], 'int') : 0;
        $section_id = isset($_POST['secid']) ? sanitizeInput($_POST['secid'], 'int') : 0; 
        $subject_id = isset($_POST['subid']) ? sanitizeInput($_POST['subid'], 'int') : 0; 

        // Convert dates to MySQL format (YYYY-MM-DD) 
        $examdate = date(DB_DATE_FORMAT,strtotime($exam_date)); 

        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'examdate', 'value' => $examdate, 'type' => 's'],
            ['name' => 'exam_start_time', 'value' => $exam_start_time, 'type' => 's'],
            ['name' => 'exam_end_time', 'value' => $exam_end_time, 'type' => 's'],
            ['name' => 'session_filter', 'value' => $session_filter, 'type' => 'i'],
            ['name' => 'exam_id', 'value' => $exam_id, 'type' => 'i'],
            ['name' => 'year_id', 'value' => $year_id, 'type' => 'i'],
            ['name' => 'section_id', 'value' => $section_id, 'type' => 'i'], 
            ['name' => 'subject_id', 'value' => $subject_id, 'type' => 'i'],
            ['name' => 'department_id', 'value' => $logged_dept_id, 'type' => 'i']

        ];
      

          $result = callProcedure('insert_faculty_exam_subject', $procedure_params); 
        //   print_r($result);
        //   exit;
        //  exit; 
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
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>