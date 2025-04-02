<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    try {
        $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject'], 'int') : 0;

        $procedure_params = [

            ['name' => 'user_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $subject, 'type' => 'i']
        ];

        $result = callProcedure('fetch_pr_section_list_attendance', $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if (isset($result['data'][0]) && is_array($result['data'][0])) {
                    $data = $result['data'][0];
                     
                    
                    
                    
                    echo json_encode([
                        'code' => $result['particulars'][0]['status_code'],
                        'status' => $result['particulars'][0]['status'],
                        'message' => $result['particulars'][0]['message'],
                        'data' => $data

                    ]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message']
                ]);
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
