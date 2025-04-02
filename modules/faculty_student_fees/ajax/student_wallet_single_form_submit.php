<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $location_href = isset($_POST['HTTP_X_Requested_Path']) ? sanitizeInput($_POST['HTTP_X_Requested_Path'], 'string') : '';
        $student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id'], 'int') : 0;
        $student_wallet_id = isset($_POST['student_wallet_id']) ? sanitizeInput(decrypt_data($_POST['student_wallet_id']), 'int') : 0;
        $wallet_date = isset($_POST['wallet_date']) ? sanitizeInput($_POST['wallet_date'], 'string') : "";
        $wallet_amount = isset($_POST['wallet_amount']) ? sanitizeInput($_POST['wallet_amount'], 'float') : "";
        $wallet_reference_id = isset($_POST['wallet_reference_id']) ? sanitizeInput($_POST['wallet_reference_id'], 'string') : "";
        $payment_mode = isset($_POST['payment_mode']) ? sanitizeInput($_POST['payment_mode'], 'int') : "";
        $wallet_remarks = isset($_POST['wallet_remarks']) ? sanitizeInput($_POST['wallet_remarks'], 'string') : "";
        $wallet_action = isset($_POST['wallet_action']) ? sanitizeInput($_POST['wallet_action'], 'int') : 1;
        $wallet_status = isset($_POST['wallet_status']) ? sanitizeInput($_POST['wallet_status'], 'int') : 1;
        $p_type = isset($_POST['p_type']) ? sanitizeInput($_POST['p_type'], 'int') : 1;


        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'student_id', 'type' => 'i', 'value' => $student_id],
            ['name' => 'student_wallet_id', 'type' => 'i', 'value' => $student_wallet_id],
            ['name' => 'wallet_date', 'type' => 's', 'value' => $wallet_date],
            ['name' => 'wallet_reference_id', 'type' => 's', 'value' => $wallet_reference_id],
            ['name' => 'wallet_amount', 'type' => 'f', 'value' => $wallet_amount],
            ['name' => 'payment_mode', 'type' => 'i', 'value' => $payment_mode],
            ['name' => 'wallet_remarks', 'type' => 's', 'value' => $wallet_remarks],
            ['name' => 'wallet_action', 'type' => 'i', 'value' => $wallet_action],
            ['name' => 'wallet_status', 'type' => 'i', 'value' => $wallet_status],
            ['name' => 'p_type', 'type' => 'i', 'value' => $p_type]
        ];

        $result = callProcedure("update_pr_single_student_wallet", $params_procedures);
        if ($result) {
            if (isset($result['particulars'][0]['status_code']) && $result['particulars'][0]['status_code'] === 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
            exit;
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, $error_side);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
