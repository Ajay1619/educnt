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

        $official_mail_id = isset($_POST['official_mail_id']) ? sanitizeInput($_POST['official_mail_id'], 'string') : '';
        $personal_mail_id = isset($_POST['personal_mail_id']) ? sanitizeInput($_POST['personal_mail_id'], 'string') : '';
        $mobile_number = isset($_POST['mobile_number']) ? sanitizeInput($_POST['mobile_number'], 'string') : '';
        $alt_mobile_number = isset($_POST['alt_mobile_number']) ? sanitizeInput($_POST['alt_mobile_number'], 'string') : '';
        $whatsapp_mobile_number = isset($_POST['whatsapp_mobile_number']) ? sanitizeInput($_POST['whatsapp_mobile_number'], 'string') : '';

        if (empty($official_mail_id)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your Official Mail ID.']);
            exit;
        }
        if (empty($personal_mail_id)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your last name.']);
            exit;
        }
        if (empty($mobile_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your initial.']);
            exit;
        }
        if (empty($alt_mobile_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your salutation.']);
            exit;
        }
        if (empty($whatsapp_mobile_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your date of birth.']);
            exit;
        }

        $procedure_params = [
            ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_in_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'official_mail_id', 'value' => $official_mail_id, 'type' => 's'],
            ['name' => 'personal_mail_id', 'value' => $personal_mail_id, 'type' => 's'],
            ['name' => 'mobile_number', 'value' => $mobile_number, 'type' => 's'],
            ['name' => 'alt_mobile_number', 'value' => $alt_mobile_number, 'type' => 's'],
            ['name' => 'whatsapp_mobile_number', 'value' => $whatsapp_mobile_number, 'type' => 's'],

        ];
        $result = callProcedure("update_pr_faculty_personal_contact_profile_info", $procedure_params);
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating Contact details.']);
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
