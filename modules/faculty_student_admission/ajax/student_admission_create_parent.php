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
        // Validate and sanitize form data
        $parent_user_name = isset($_POST['parent_user_name']) ? sanitizeInput($_POST['parent_user_name'], 'string') : '';
        $parent_first_name = isset($_POST['parent_first_name']) ? sanitizeInput($_POST['parent_first_name'], 'string') : '';
        $parent_middle_name = isset($_POST['parent_middle_name']) ? sanitizeInput($_POST['parent_middle_name'], 'string') : '';
        $parent_last_name = isset($_POST['parent_last_name']) ? sanitizeInput($_POST['parent_last_name'], 'string') : '';
        $parent_initial = isset($_POST['parent_initial']) ? sanitizeInput($_POST['parent_initial'], 'string') : '';
        $parent_mobile_number = isset($_POST['parent_mobile_number']) ? sanitizeInput($_POST['parent_mobile_number'], 'string') : '';
        $parent_email_id = isset($_POST['parent_email_id']) ? sanitizeInput($_POST['parent_email_id'], 'string') : null;
        $parent_code = isset($_POST['parent_code']) ? sanitizeInput($_POST['parent_code'], 'int') : 0;
        $parent_role = isset($_POST['parent_role']) ? sanitizeInput($_POST['parent_role'], 'int') : 0;
        $student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id'], 'int') : 0;
        $relationship_type = isset($_POST['relation_type']) ? sanitizeInput($_POST['relation_type'], 'int') : 0;
        

        // Validate required fields
        if (empty($parent_first_name)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'First Name is required.']);
            exit;
        }
        
        if (empty($parent_initial)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Initial is required.']);
            exit;
        }
        if (empty($parent_mobile_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Mobile Number is required.']);
            exit;
        }

        // Prepare parameters for stored procedure
        $procedure_params = [
            ['name' => 'parent_user_name', 'value' => $parent_user_name, 'type' => 's'],
            ['name' => 'parent_first_name', 'value' => $parent_first_name, 'type' => 's'],
            ['name' => 'parent_middle_name', 'value' => $parent_middle_name, 'type' => 's'],
            ['name' => 'parent_last_name', 'value' => $parent_last_name, 'type' => 's'],
            ['name' => 'parent_initial', 'value' => $parent_initial, 'type' => 's'],
            ['name' => 'parent_mobile_number', 'value' => $parent_mobile_number, 'type' => 's'],
            ['name' => 'parent_email_id', 'value' => $parent_email_id, 'type' => 's'],
            ['name' => 'parent_code', 'value' => $parent_code, 'type' => 'i'],
            ['name' => 'parent_role', 'value' => $parent_role, 'type' => 'i'],
            ['name' => 'parent_type', 'value' => 3, 'type' => 'i'],
            ['name' => 'student_id', 'value' => $student_id, 'type' => 'i'],
            ['name' => 'relationship_type', 'value' => $relationship_type, 'type' => 'i'] // Added this parameter
        ];
        
      
        // Call stored procedure to save data
        $result = callProcedure("insert_pr_create_parent_account", $procedure_params);

        //  print_r($result);
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
