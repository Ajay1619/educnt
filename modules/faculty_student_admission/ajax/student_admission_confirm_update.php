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
        // $existing_id = isset($_POST['admission_student_existing']) ? sanitizeInput($_POST['admission_student_existing']) : 0;
        // $mailid = isset($_POST['mail_id']) ? sanitizeInput($_POST['mail_id'],'string') : '';
        $admission_type = isset($_POST['admission_type']) ? sanitizeInput($_POST['admission_type'],'string') : '';
        $student_username = isset($_POST['student_username']) ? sanitizeInput($_POST['student_username'],'string') : '';
        $student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id'],'int') : 0;
        $last_account_id = isset($_POST['account_id']) ? sanitizeInput($_POST['account_id'],'int') : 0;
        $account_number = isset($_POST['account_number']) ? sanitizeInput($_POST['account_number'],'int') : 0;
        // $portal_type = isset($_POST['portal_type']) ? sanitizeInput($_POST['portal_type'],'int') : 0;
        
        if (empty($admission_type)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select academic batch.']);
            exit;
        }
        // if (empty($account_number)) {
        //     echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Contact Admin user name is not generated.']);
        //     exit;
        // }
       
       
        
        $procedure_params = [
            ['name' => 'student_id', 'value' => $student_id, 'type' => 0],
            ['name' => 'admission_type', 'value' => $admission_type, 'type' => 's'],
            ['name' => 'last_account_id', 'value' => $last_account_id, 'type' => 0],
            ['name' => 'student_username', 'value' => $student_username, 'type' => 's']
            
        ];
        $result = callProcedure("update_confirmation_student_admission", $procedure_params);
   
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                // $_SESSION['admission_student_existing'] = $result['particulars'][0]['existing_student_id'];
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
