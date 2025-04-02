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
        $Ist_concession_category = isset($_POST['Ist_concession_category']) ? sanitizeInput($_POST['Ist_concession_category'], 'string') : [];
        $IInd_concession_category = isset($_POST['IInd_concession_category']) ? sanitizeInput($_POST['IInd_concession_category'], 'string') : [];
        $IIIrd_concession_category = isset($_POST['IIIrd_concession_category']) ? sanitizeInput($_POST['IIIrd_concession_category'], 'string') : [];
        $IVth_concession_category = isset($_POST['IVth_concession_category']) ? sanitizeInput($_POST['IVth_concession_category'], 'string') : [];

        $Ist_concession_amount = isset($_POST['Ist_concession_amount']) ? sanitizeInput($_POST['Ist_concession_amount'], 'int') : [];
        $IInd_concession_amount = isset($_POST['IInd_concession_amount']) ? sanitizeInput($_POST['IInd_concession_amount'], 'int') : [];
        $IIIrd_concession_amount = isset($_POST['IIIrd_concession_amount']) ? sanitizeInput($_POST['IIIrd_concession_amount'], 'int') : [];
        $IVth_concession_amount = isset($_POST['IVth_concession_amount']) ? sanitizeInput($_POST['IVth_concession_amount'], 'int') : [];

        $existingAdmissionValue = isset($existingAdmissionValue) ? sanitizeInput($existingAdmissionValue, 'int') : 0;


        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'student_id', 'value' => $existingAdmissionValue, 'type' => 'i'],
            ['name' => 'Ist_concession_category', 'value' => json_encode($Ist_concession_category), 'type' => 's'],
            ['name' => 'IInd_concession_category', 'value' => json_encode($IInd_concession_category), 'type' => 's'],
            ['name' => 'IIIrd_concession_category', 'value' => json_encode($IIIrd_concession_category), 'type' => 's'],
            ['name' => 'IVth_concession_category', 'value' => json_encode($IVth_concession_category), 'type' => 's'],
            ['name' => 'Ist_concession_amount', 'value' => json_encode($Ist_concession_amount), 'type' => 's'],
            ['name' => 'IInd_concession_amount', 'value' => json_encode($IInd_concession_amount), 'type' => 's'],
            ['name' => 'IIIrd_concession_amount', 'value' => json_encode($IIIrd_concession_amount), 'type' => 's'],
            ['name' => 'IVth_concession_amount', 'value' => json_encode($IVth_concession_amount), 'type' => 's'],
        ];

        $result = callProcedure("update_pr_student_concession_structure", $procedure_params);

        if ($result) {

            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ']);
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
