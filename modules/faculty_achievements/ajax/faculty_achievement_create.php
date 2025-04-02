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
        
    // Sanitize and set achievement parameters
    $achievements = isset($_POST['achievements']) ? sanitizeInput($_POST['achievements'], 'string') : '';
    $achievement_type = isset($_POST['achievement_type']) ? sanitizeInput($_POST['achievement_type'], 'string') : '';
    $achievement_topic = isset($_POST['achievement_topic']) ? sanitizeInput($_POST['achievement_topic'], 'string') : '';
    $achievement_date = isset($_POST['achievement_date']) ? sanitizeInput($_POST['achievement_date'], 'string') : '';
    $venue_location = isset($_POST['achievement_name']) ? sanitizeInput($_POST['achievement_name'], 'string') : '';
    
    $file_link = '';
    // Handle file upload if a file is provided
    if (isset($_FILES['file_upload'])) {
        // Prepare achievement type as prefix by replacing spaces with underscores
        $formatted_achievement_type = str_replace(' ', '_', $achievements);
    
        // Append current date and time to make filename unique
        $timestamp = date('Ymd_His');
        $file_prefix = "{$formatted_achievement_type}_ACHV-{$timestamp}";
    
        // Call the upload function with the formatted prefix
        $upload_result = uploadFile($_FILES['file_upload'], ROOT . '/global/uploads/faculty_achievements', $file_prefix);
    
        // Check upload result and handle response
        if ($upload_result['status_code'] == 200) {
            $file_link = $upload_result['files'][0];
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in uploading the file.']);
            exit;
        }
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No file uploaded.']);
        exit;
    }
    

    // Set parameters for procedure call
    $procedure_params = [
        ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
        ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
        ['name' => 'achievement_type', 'value' => $achievement_type, 'type' => 's'],
        ['name' => 'achievement_topic', 'value' => $achievement_topic, 'type' => 's'],
        ['name' => 'achievement_date', 'value' => date(DB_DATE_FORMAT, strtotime($achievement_date)), 'type' => 's'],
        ['name' => 'venue_location', 'value' => $venue_location, 'type' => 's'],
        ['name' => 'file_link', 'value' => $file_link, 'type' => 's']
    ];
    $result = callProcedure("insert_pr_faculty_achievements", $procedure_params);

    // Handle result and send JSON response
    if ($result && $result['particulars'][0]['status_code'] == 200) {
        echo json_encode([
            'code' => $result['particulars'][0]['status_code'],
            'status' => $result['particulars'][0]['status'],
            'message' => $result['particulars'][0]['message']
        ]);
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