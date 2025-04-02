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
        $achievement_id = isset($_POST['achievement_id']) ? sanitizeInput($_POST['achievement_id'], 'int') : '';
         $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int') : '';  // Ensure this is passed from POST or session
        $achievement_type = isset($_POST['achievement_type']) ? sanitizeInput($_POST['achievement_type'], 'int') : '';
        $achievement_topic = isset($_POST['achievement_topic']) ? sanitizeInput($_POST['achievement_topic'], 'string') : '';
        $achievement_date = isset($_POST['achievement_date']) ? sanitizeInput($_POST['achievement_date'], 'string') : '';
        $venue_location = isset($_POST['achievement_name']) ? sanitizeInput($_POST['achievement_name'], 'string') : '';

        // Handle file upload if a file is provided
        $file_link = '';
        if (isset($_FILES['file_uploads'])) {
            // Prepare achievement type as prefix by replacing spaces with underscores
            $formatted_achievement_type = str_replace(' ', '_', $achievement_type);
            $timestamp = date('Ymd_His');
            $file_prefix = "{$formatted_achievement_type}_ACHV-{$timestamp}";

            // Call the upload function with the formatted prefix
            $upload_result = uploadFile($_FILES['file_uploads'], ROOT . '/global/uploads/faculty_achievements', $file_prefix);
            $file_link = $upload_result['files'][0] ?? ''; // Ensure file link is set
        }

        // Set parameters for procedure call
        $procedure_params = [
            ['name' => 'achievement_id', 'value' => $achievement_id, 'type' => 'i'],
            ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'achievement_type', 'value' => $achievement_type, 'type' => 'i'],
            ['name' => 'achievement_topic', 'value' => $achievement_topic, 'type' => 's'],
            ['name' => 'achievement_date', 'value' => date(DB_DATE_FORMAT, strtotime($achievement_date)), 'type' => 's'],
            ['name' => 'venue_location', 'value' => $venue_location, 'type' => 's'],
            ['name' => 'file_link', 'value' => $file_link, 'type' => 's']
        ];

        // Call procedure to save achievement details
        $result = callProcedure("update_pr_faculty_achievement_record", $procedure_params);

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
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error Occured : ' . $th->getMessage()]);
        }
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }