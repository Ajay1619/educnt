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
          $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
          $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
          $exam_id = isset($_POST['exam_id']) ? sanitizeInput($_POST['exam_id'], 'int') : 0;
           $subject_id = isset($_POST['subjectid']) ? sanitizeInput($_POST['subjectid'], 'int') : 0; 
        // $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        // $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;




        $params_procedures = [
            
            ['name' => 'exam_id', 'type' => 'i', 'value' => $exam_id], 
             ['name' => 'subject_id', 'type' => 'i', 'value' => $subject_id],
            ['name' => 'year_of_study_id', 'type' => 'i', 'value' => $year_of_study_id],
             ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
             ['name' => 'section_id', 'type' => 'i', 'value' => $section_id] 
            // ['name' => 'section_id', 'type' => 'i', 'value' => $section_id] 
        ]; 
        $result = callProcedure("fetch_date_session", $params_procedures);  
        // print_r($params_procedures);
        // print_r($result);
        // exit;
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if ($result['data']) {
                    $data = $result['data']; 
                    // $data[0][0]['exam_start_date'] = date("d-m-Y", strtotime($data[0][0]['exam_start_date']));
                    // $data[0][0]['exam_end_date'] = date(BULMA_DATE_FORMAT, strtotime($data[0][0]['exam_end_date']));
                     
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $data, 'message' => $result['particulars'][0]['message']]);
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
