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
        $location_href = isset($_POST['location']) ? sanitizeInput($_POST['location'], 'string') : '';
        $params_procedures = [
            ['name' => 'faculty_id', 'type' => 'i', 'value' => $logged_user_id],
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id]
        ];
        $result = callProcedure("fetch_pr_individual_class_advisor_details", $params_procedures);

        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0][0])) {
                    $data = $result['data'][0][0];
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $data, 'message' => $result['particulars'][0]['message']]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
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
