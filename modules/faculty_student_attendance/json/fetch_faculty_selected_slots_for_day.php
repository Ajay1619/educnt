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
    $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    try {
        $date = isset($_POST['date']) ? sanitizeInput($_POST['date'], 'string') : '';
        $subject_id = isset($_POST['subject_id']) ? sanitizeInput($_POST['subject_id'], 'int') : 0;
        $day_id = date('w', strtotime($date)) + 1;
        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'day_id', 'value' => $day_id, 'type' => 'i'],
            ['name' => 'subject_id', 'value' => $subject_id, 'type' => 'i']
        ];

        $result = callProcedure('fetch_pr_faculty_selected_slots_for_day', $procedure_params);
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
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No Slots Found For Selected Subject And Date.']);
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
