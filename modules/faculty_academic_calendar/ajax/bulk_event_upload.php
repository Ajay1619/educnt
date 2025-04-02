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


     $events_data = isset($_POST['events_data']) ? sanitizeInput($_POST['events_data'], 'string') : '';
      $events_data = isset($_POST['events_data']) ? sanitizeInput($_POST['events_data'], 'string') : '';


    $procedure_params = [
        ['name' => 'events_data', 'type' => 's', 'value' => json_encode($events_data)],
        ['name' => 'logged_id', 'type' => 's', 'value' => $logged_login_id],
    ];

    $result = callProcedure("insert_faculty_events", $procedure_params); 
 
        if ($result) {
            
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating personal details.']);
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

