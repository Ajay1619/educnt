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
        //  print_r($_POST);
          $year_of_study_id = isset($_POST['data']['selectedYearOfStudyId']) ? sanitizeInput($_POST['data']['selectedYearOfStudyId'], 'int') : 0;
          $section_id = isset($_POST['data']['selectedSection']) ? sanitizeInput($_POST['data']['selectedSection'], 'int') : 0;
        // $dep_id = isset($_POST['exam_id']) ? sanitizeInput($_POST['exam_id'], 'int') : 0;
        //    $subject_id = isset($_POST['subjectid']) ? sanitizeInput($_POST['subjectid'], 'int') : 0; 
           $exam_id = isset($_POST['examid']) ? sanitizeInput($_POST['examid'], 'int') : 0; 
           $subject_id = isset($_POST['data']['selectedSubject']) ? sanitizeInput($_POST['data']['selectedSubject'], 'int') : 0; 
        // $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        // $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;




        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'depid', 'type' => 'i', 'value' => $logged_dept_id], 
             ['name' => 'yearid', 'type' => 'i', 'value' => $year_of_study_id], 
             ['name' => 'section_id', 'type' => 'i', 'value' => $section_id], 
                ['name' => 'exam_id', 'type' => 'i', 'value' => $exam_id],
                ['name' => 'subject_id', 'type' => 'i', 'value' => $subject_id] 
            // ['name' => 'section_id', 'type' => 'i', 'value' => $section_id] 
        ];   
        $result = callProcedure("fetch_student_exam", $params_procedures); 
        //   print_r($result);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if ($result['data']) {
                    $data = $result['data']; 
                    // print_r($data);
                    // $data[0][0]['exam_start_date'] = date("d-m-Y", strtotime($data[0][0]['exam_start_date']));
                    // $data[0][0]['exam_end_date'] = date(BULMA_DATE_FORMAT, strtotime($data[0][0]['exam_end_date']));
                    if (isset($data[0][1])) {
                        echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $data, 'message' => $result['particulars'][0]['message']]);
                        exit;
                    } else {
                        echo json_encode(['code' => 200, 'status' => 'error', 'data' => $data, 'message' => 'No Year Of Study Found For Selected Subject.']);
                        exit;
                    }
                     
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
