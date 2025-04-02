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
        $Ist_fee_category = isset($_POST['Ist_fee_category']) ? sanitizeInput($_POST['Ist_fee_category'], 'int') : [];
        $IInd_fee_category = isset($_POST['IInd_fee_category']) ? sanitizeInput($_POST['IInd_fee_category'], 'int') : [];
        $IIIrd_fee_category = isset($_POST['IIIrd_fee_category']) ? sanitizeInput($_POST['IIIrd_fee_category'], 'int') : [];
        $IVth_fee_category = isset($_POST['IVth_fee_category']) ? sanitizeInput($_POST['IVth_fee_category'], 'int') : [];

        $Ist_fee_amount = isset($_POST['Ist_fee_amount']) ? sanitizeInput($_POST['Ist_fee_amount'], 'int') : [];
        $IInd_fee_amount = isset($_POST['IInd_fee_amount']) ? sanitizeInput($_POST['IInd_fee_amount'], 'int') : [];
        $IIIrd_fee_amount = isset($_POST['IIIrd_fee_amount']) ? sanitizeInput($_POST['IIIrd_fee_amount'], 'int') : [];
        $IVth_fee_amount = isset($_POST['IVth_fee_amount']) ? sanitizeInput($_POST['IVth_fee_amount'], 'int') : [];

        $existingAdmissionValue = isset($existingAdmissionValue) ? sanitizeInput($existingAdmissionValue, 'int') : 0;


        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'student_id', 'value' => $existingAdmissionValue, 'type' => 'i'],
            ['name' => 'Ist_fee_category', 'value' => json_encode($Ist_fee_category), 'type' => 's'],
            ['name' => 'IInd_fee_category', 'value' => json_encode($IInd_fee_category), 'type' => 's'],
            ['name' => 'IIIrd_fee_category', 'value' => json_encode($IIIrd_fee_category), 'type' => 's'],
            ['name' => 'IVth_fee_category', 'value' => json_encode($IVth_fee_category), 'type' => 's'],
            ['name' => 'Ist_fee_amount', 'value' => json_encode($Ist_fee_amount), 'type' => 's'],
            ['name' => 'IInd_fee_amount', 'value' => json_encode($IInd_fee_amount), 'type' => 's'],
            ['name' => 'IIIrd_fee_amount', 'value' => json_encode($IIIrd_fee_amount), 'type' => 's'],
            ['name' => 'IVth_fee_amount', 'value' => json_encode($IVth_fee_amount), 'type' => 's'],
        ];

        $result = callProcedure("update_pr_student_fees_structure", $procedure_params);

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
