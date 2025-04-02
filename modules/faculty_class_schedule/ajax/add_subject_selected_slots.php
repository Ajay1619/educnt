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
        $selected_day = isset($_POST['selected_day']) ? sanitizeInput($_POST['selected_day'], 'int') : 0;
        $subject_id = isset($_POST['subject_id']) ? sanitizeInput($_POST['subject_id'], 'int') : 0;
        $sem_duration_id = isset($_POST['sem_duration_id']) ? sanitizeInput($_POST['sem_duration_id'], 'int') : 0;

        $selected_slots_list = isset($_POST['selected_slots_list']) ? sanitizeInput($_POST['selected_slots_list'], 'int') : [];

        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'selected_day', 'type' => 'i', 'value' => $selected_day],
            ['name' => 'subject_id', 'type' => 'i', 'value' => $subject_id],
            ['name' => 'sem_duration_id', 'type' => 'i', 'value' => $sem_duration_id],
            ['name' => 'selected_slots_list', 'type' => 's', 'value' => json_encode($selected_slots_list)]
        ];
        $result = callProcedure("add_pr_faculty_subject_selected_slots", $params_procedures);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
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
        insert_error($error_message, $_SERVER['HTTP_X_CSRF_TOKEN'], $error_side);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
