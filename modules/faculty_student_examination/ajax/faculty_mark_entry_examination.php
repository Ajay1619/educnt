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
// print_r($_POST);
        // marks[]: 75
        // student_id[]: 3
        // student_official_id[]: 30
        // examid[]: 72
        // yearid[]: 51
        // sectionid[]: 43
        // subjectid[]: 31
        // deptid[]: 1
        // semid[]: 57


 
        $exam_marks = isset($_POST['marks']) ? sanitizeInput($_POST['marks'], 'float') : 0;
        $exam_student_official_id = isset($_POST['student_official_id']) ? sanitizeInput($_POST['student_official_id'], 'int') : 0;
        $exam_student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id'], 'int') : 0;
        $exam_examid = isset($_POST['examid']) ? sanitizeInput($_POST['examid'], 'int') : 0;  
        $exam_yearid = isset($_POST['yearid']) ? sanitizeInput($_POST['yearid'], 'int') : 0;
        $exam_sectionid = isset($_POST['sectionid']) ? sanitizeInput($_POST['sectionid'], 'int') : 0;
        $exam_subjectid = isset($_POST['subjectid']) ? sanitizeInput($_POST['subjectid'], 'int') : 0;
        $exam_deptid = isset($_POST['deptid']) ? sanitizeInput($_POST['deptid'], 'int') : 0;
        $exam_semid = isset($_POST['semid']) ? sanitizeInput($_POST['semid'], 'int') : 0;  
        $reexam_marks = isset($_POST['reexam_marks']) ? sanitizeInput($_POST['reexam_marks'], 'float') : 0;  
         

        // 
        // ['name' => 'exam_student_official_id', 'value' => json_encode($exam_student_official_id), 'type' => 's'],
        // ['name' => 'exam_examid', 'value' => json_encode($exam_examid), 'type' => 's'],
        // ['name' => 'exam_yearid', 'value' => json_encode($exam_yearid), 'type' => 's'],
        // ['name' => 'exam_sectionid', 'value' => json_encode($exam_sectionid), 'type' => 's'],
        // 


        $procedure_params = [
        ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'], 
        ['name' => 'exam_marks', 'value' =>json_encode($exam_marks) , 'type' => 's'],
        ['name' => 'exam_student_id', 'value' => json_encode($exam_student_id), 'type' => 's'], 
        ['name' => 'exam_semdurid', 'value' => json_encode($exam_semid), 'type' => 's'] ,
        ['name' => 'exam_examid', 'value' => json_encode($exam_examid), 'type' => 's'],
        ['name' => 'exam_deptid', 'value' => json_encode($exam_deptid), 'type' => 's'], 
        ['name' => 'exam_subjectid', 'value' => json_encode($exam_subjectid), 'type' => 's'],
        ['name' => 'reexam_marks', 'value' => json_encode($reexam_marks), 'type' => 's'],

        ];
        

        // print_r($procedure_params);

         $result = callProcedure('insert_or_update_faculty_exam_marks', $procedure_params); 
//       
        if ($result) {
            // print_r($result);
            if ($result['particulars'][0]['status_code'] == 200) {
                $data = $result['data']; 

                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message'],
                     
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