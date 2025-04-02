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
        
        $account_id = isset($_POST['account_id']) ? sanitizeInput($_POST['account_id'],'int') : 0;
        
        
       
        // if (empty($account_number)) {
        //     echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Contact Admin user name is not generated.']);
        //     exit;
        // }
       
       
        
        $procedure_params = [
           
            ['name' => 'account_id', 'value' => $account_id, 'type' => 0],
            ['name' => 'p_login_id', 'type' => 'i', 'value' => $logged_login_id]
            
            
        ];
        // print_r($procedure_params);
         $result = callProcedure("delete_new_account_data", $procedure_params);
    //    print_r($result);
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
