<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

    try {
        $procedure_params = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id]
        ];
        $result = callProcedure('fetch_pr_institution_logo', $procedure_params);
        
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                $data = $result['data'][0][0];
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data]);
                exit;
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Failed to retrieve response.']);
            exit;
        }
    } catch (\Throwable $th) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
