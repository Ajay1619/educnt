<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $procedure_params = [
            ['name' => 'p_portal_type', 'value' => 2, 'type' => 'i']
        ];

        $result = callProcedure("fetch_pr_dev_new_account_code_and_roles", $procedure_params);
        //  print_r($result);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                $username = $result['particulars'][0]['account_username'];
                $account_id = $result['particulars'][0]['account_id'];
                // $username_data = $result['data'][0][0];
                // $role_data = $result['data'][1][0];
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'],'username' => $username,'account_id' => $account_id]);
                exit;
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
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
