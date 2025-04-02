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
        $location_href = isset($_POST['HTTP_X_Requested_Path']) ? sanitizeInput($_POST['HTTP_X_Requested_Path'], 'string') : '';
        $faculty_subjects_id = isset($_POST['faculty_subjects_id']) ? sanitizeInput(decrypt_data($_POST['faculty_subjects_id']), 'int') : 0;
        $attendance_date = isset($_POST['attendance_date']) ? sanitizeInput(decrypt_data($_POST['attendance_date']), 'string') : 0;
        $selected_attendance_slot = isset($_POST['selected_attendance_slot']) ? sanitizeInput(decrypt_data($_POST['selected_attendance_slot']), 'int') : 0;


        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_subject_id', 'type' => 'i', 'value' => $faculty_subjects_id],
            ['name' => 'attendance_date', 'type' => 's', 'value' => date(DB_DATE_FORMAT, strtotime($attendance_date))],
            ['name' => 'selected_attendance_slot', 'type' => 'i', 'value' => $selected_attendance_slot],
            ['name' => 'student_attendance_group_ids', 'type' => 'i', 'value' => json_encode([])],
            ['name' => 'student_attendance_status', 'type' => 'i', 'value' => json_encode([])],
            ['name' => 'student_attendance_permission', 'type' => 'i', 'value' => json_encode([])],
            ['name' => 'student_attendance_note', 'type' => 'i', 'value' => json_encode([])],
            ['name' => 'attendance_student_id', 'type' => 'i', 'value' => json_encode([])],
            ['name' => 'p_type', 'type' => 'i', 'value' => 2],
        ];

        $result = callProcedure("update_pr_student_attendance_entry", $params_procedures);
        if ($result) {
            if (isset($result['particulars'][0]['status_code']) && $result['particulars'][0]['status_code'] === 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            } else {
                echo json_encode(['code' => $result['status_code'], 'status' => $result['status'], 'message' => $result['message']]);
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
