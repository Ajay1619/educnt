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
    try {

        // Get all the POST data and sanitize inputs
        $first_course_preference = isset($_POST['1st_course_preference']) ? sanitizeInput($_POST['1st_course_preference'], 'int') : 0;
        $second_course_preference = isset($_POST['2nd_course_preference']) ? sanitizeInput($_POST['2nd_course_preference'], 'int') : 0;
        $third_course_preference = isset($_POST['3rd_course_preference']) ? sanitizeInput($_POST['3rd_course_preference'], 'int') : 0;
        $student_reference_name = isset($_POST['student_reference_name']) ? sanitizeInput($_POST['student_reference_name'], 'int') : 0;

        // Other fields based on your procedure
        $student_transport_name = isset($_POST['student_transport_name']) ? sanitizeInput($_POST['student_transport_name'], 'int') : 0;
        $student_know_about_us = isset($_POST['student_know_about_us']) ? sanitizeInput($_POST['student_know_about_us'], 'int') : 0;
        $student_concession = isset($_POST['student_concession']) ? sanitizeInput($_POST['student_concession'], 'int') : 0;
        $type_of_admission_status = isset($_POST['type_of_admission_status']) ? sanitizeInput($_POST['type_of_admission_status'], 'int') : 0;
        $student_residency_status = isset($_POST['student_residency_status']) ? sanitizeInput($_POST['student_residency_status'], 'int') : 0;
        $lateral_entry = isset($_POST['lateral_entry']) ? sanitizeInput($_POST['lateral_entry'], 'int') : 0;
        $register_number = isset($_POST['register_number']) ? sanitizeInput($_POST['register_number'], 'string') : '';
        $continuing_year = isset($_POST['continuing_year']) ? sanitizeInput($_POST['continuing_year'], 'string') : '';

        // Check for required fields
        // if ( $first_course_preference == 0  ) {
        //     echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You Skiped course Information!']);
        //     exit;
        // } 
            
        if (empty($student_reference_name)) {
            $student_reference_name = null;
        }
        if (empty($first_course_preference)) {
            $first_course_preference = null;
        }
        if (empty($second_course_preference)) {
            $second_course_preference = null;
        }
        if (empty($third_course_preference)) {
            $third_course_preference = null;
        }
        // Assuming you have a logged-in user and their ID is available as $logged_login_id
        // $logged_login_id = 1; // Replace this with actual logged-in user ID

        // Prepare procedure parameters
        // echo $existingAdmissionValue;
        $procedure_params = [
            ['name' => 'student_admission_student_id', 'type' => 'i', 'value' => $existingAdmissionValue], // Assuming you are using the logged-in user's ID
            ['name' => 'student_admission_type', 'type' => 'i', 'value' => $type_of_admission_status],
            ['name' => 'student_admission_category', 'type' => 'i', 'value' => $lateral_entry], // Same as admission status
            ['name' => 'student_hostel', 'type' => 'i', 'value' => $student_residency_status],
            ['name' => 'student_admission_know_about_us', 'type' => 'i', 'value' => $student_know_about_us],
            ['name' => 'student_transport', 'type' => 'i', 'value' => $student_transport_name],
            ['name' => 'student_reference', 'type' => 'i', 'value' => $student_reference_name],
            ['name' => 'student_admission_reg_no', 'type' => 's', 'value' => $register_number],
            ['name' => 'student_course_preference1', 'type' => 'i', 'value' => $first_course_preference],
            ['name' => 'student_course_preference2', 'type' => 'i', 'value' => $second_course_preference],
            ['name' => 'student_course_preference3', 'type' => 'i', 'value' => $third_course_preference],
            ['name' => 'student_concession_subject', 'type' => 'i', 'value' => $student_concession],
            ['name' => 'student_concession_body', 'type' => 's', 'value' => $continuing_year],
            // ['name' => 'admission_status', 'type' => 'i', 'value' => $type_of_admission_status],
            ['name' => 'admission_deleted', 'type' => 'i', 'value' => 0], // Assuming not deleted
            ['name' => 'lateral_entry_year_of_study', 'type' => 'i', 'value' => $lateral_entry],
            ['name' => 'p_login_id', 'type' => 'i', 'value' => $logged_login_id], // Logged-in user ID
        ];
        //   print_r($procedure_params);
        // Call procedure
        $result = callProcedure("update_update_student_admission_info", $procedure_params);
//    print_r($result);
        // Check if the procedure was successful
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating student admission information.']);
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
