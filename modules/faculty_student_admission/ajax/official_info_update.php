<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX POST request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        // Validate form data
        $existing_id = isset($_POST['admission_student_existing']) ? sanitizeInput($_POST['admission_student_existing']) : 0;
        $mailid = isset($_POST['mail_id']) ? sanitizeInput($_POST['mail_id'],'string') : '';
        $registernumber = isset($_POST['register_number']) ? sanitizeInput($_POST['register_number'],'string') : '';
     
        if (empty($mailid) ) {
            $mailid = NULL;
        }
       
        
        if (empty($registernumber)) {
            $registernumber = NULL;
           
        }
      
        
        $procedure_params = [
            ['name' => 'existing_id', 'value' => $existingAdmissionValue, 'type' => 0],
            ['name' => 'official Mailid', 'value' => $mailid, 'type' => 's'],
            ['name' => 'registernumber', 'value' => $registernumber, 'type' => 's'],
            // ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 's'],
           
            
        ];
        
        $result = callProcedure("insert_pr_create_addmission_official", $procedure_params);
         
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                
                // echo $_SESSION['admission_student_existing'];
                // $logged_profile_status = isset($_SESSION['svcet_educnt_faculty_profile_status']) ? $_SESSION['svcet_educnt_faculty_profile_status'] : 0;
                exit;
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
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
