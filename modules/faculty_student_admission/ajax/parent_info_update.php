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
        // Retrieve and sanitize form data
        $existing_id = isset($_POST['admission_student_existing']) ? sanitizeInput($_POST['admission_student_existing'], 'int') : 0;
        $father_occupation = isset($_POST['father_occupation']) ? sanitizeInput($_POST['father_occupation'], 'string') : '';
        $father_name = isset($_POST['father_name']) ? sanitizeInput($_POST['father_name'], 'string') : '';
        $mother_name = isset($_POST['mother_name']) ? sanitizeInput($_POST['mother_name'], 'string') : '';
        $mother_occupation = isset($_POST['mother_occupation']) ? sanitizeInput($_POST['mother_occupation'], 'string') : '';
        $guardian_name = isset($_POST['guardian_name']) ? sanitizeInput($_POST['guardian_name'], 'string') : '';
        $guardian_occupation = isset($_POST['guardian_occupation']) ? sanitizeInput($_POST['guardian_occupation'], 'string') : '';

        // Validate required fields
        if (empty($father_name) && empty($mother_name)) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You skiped the Parent Details.']);
            exit;
        }
       
       
        

        // Define parameters for the stored procedure
        $procedure_params = [
            ['name' => 'existing_id', 'value' => $existingAdmissionValue, 'type' => 0],
            ['name' => 'father_name', 'value' => $father_name, 'type' => 's'],
            ['name' => 'father_occupation', 'value' => $father_occupation, 'type' => 's'],
            ['name' => 'mother_name', 'value' => $mother_name, 'type' => 's'],
            ['name' => 'mother_occupation', 'value' => $mother_occupation, 'type' => 's'],
            ['name' => 'guardian_name', 'value' => $guardian_name, 'type' => 's'],
            ['name' => 'guardian_occupation', 'value' => $guardian_occupation, 'type' => 's']
        ];

        // Call the stored procedure
        $result = callProcedure("insert_stu_create_admission_parent", $procedure_params);

        // Process the result from the stored procedure
        if ($result && isset($result['particulars'][0])) {
            $response = $result['particulars'][0];
            echo json_encode([
                'code' => $response['status_code'],
                'status' => $response['status'],
                'message' => $response['message'],
                'existing_student_id' => $response['existing_student_id']
            ]);

            if ($response['status_code'] == 200) {
                $_SESSION['admission_student_existing'] = $response['existing_student_id'];
            }
            exit;
        } else {
            throw new Exception('Database error: Unable to retrieve response from the procedure.');
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
