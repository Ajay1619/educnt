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
        
        $eventName = isset($_POST['eventName']) ? sanitizeInput($_POST['eventName'], 'string') : '';
        $eventDescription = isset($_POST['eventDescription']) ? sanitizeInput($_POST['eventDescription'], 'string') : '';
        $eventStartDate = isset($_POST['eventStartDate']) ? sanitizeInput($_POST['eventStartDate'], 'string') : '';
        $eventEndDate = isset($_POST['eventEndDate']) ? sanitizeInput($_POST['eventEndDate'], 'string') : '';
        $event_filter = isset($_POST['event_filter']) ? sanitizeInput($_POST['event_filter'], 'int') : 0;
        
        $event_Start_Date = date(DB_DATE_FORMAT,strtotime($eventStartDate));
        $event_End_Date = date(DB_DATE_FORMAT,strtotime($eventEndDate));

        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'], 
            ['name' => 'event_name', 'value' => $eventName, 'type' => 's'],
            ['name' => 'event_description', 'value' => $eventDescription, 'type' => 's'],
            ['name' => 'event_start_date', 'value' => $event_Start_Date, 'type' => 's'],
            ['name' => 'event_end_date', 'value' => $event_End_Date, 'type' => 's'],
            ['name' => 'event_filter', 'value' => $event_filter, 'type' => 'i']
        ];

        $result = callProcedure('insert_pr_event_single', $procedure_params);

        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                $data = $result['data'];
                 

                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message'],
                    'data' => $data
                ]);
                exit;
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
?>