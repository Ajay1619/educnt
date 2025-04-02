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
        $data = [];
        parse_str($_POST['data'], $data);
        $location_href = isset($_POST['HTTP_X_Requested_Path']) ? sanitizeInput($_POST['HTTP_X_Requested_Path'], 'string') : '';
        $faculty_subject_id = isset($data['selected_attendance_subject']) ? sanitizeInput($data['selected_attendance_subject'], 'int') : 0;
        $attendance_date = isset($data['attendance_date']) ? sanitizeInput($data['attendance_date'], 'string') : '';
        $selected_attendance_slot = isset($data['selected_attendance_slots']) ? sanitizeInput($data['selected_attendance_slots'], 'int') : 0;
        $student_attendance_group_ids = isset($_POST['group_ids']) ? sanitizeInput($_POST['group_ids'], 'int') : [];
        $student_attendance_status = isset($data['student_attendance_status']) ? sanitizeInput($data['student_attendance_status'], 'int') : [];
        $student_attendance_permission = isset($data['student_attendance_permission']) ? sanitizeInput($data['student_attendance_permission'], 'int') : [];
        $student_attendance_note = isset($data['student_attendance_note']) ? sanitizeInput($data['student_attendance_note'], 'string') : [];
        $attendance_student_id = isset($data['attendance_student_id']) ? sanitizeInput($data['attendance_student_id'], 'int') : [];

        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_subject_id', 'type' => 'i', 'value' => $faculty_subject_id],
            ['name' => 'attendance_date', 'type' => 'i', 'value' => date(DB_DATE_FORMAT, strtotime($attendance_date))],
            ['name' => 'selected_attendance_slot', 'type' => 'i', 'value' => $selected_attendance_slot],
            ['name' => 'student_attendance_group_ids', 'type' => 'i', 'value' => json_encode($student_attendance_group_ids)],
            ['name' => 'student_attendance_status', 'type' => 'i', 'value' => json_encode($student_attendance_status)],
            ['name' => 'student_attendance_permission', 'type' => 'i', 'value' => json_encode($student_attendance_permission)],
            ['name' => 'student_attendance_note', 'type' => 'i', 'value' => json_encode($student_attendance_note)],
            ['name' => 'attendance_student_id', 'type' => 'i', 'value' => json_encode($attendance_student_id)],
            ['name' => 'p_type', 'type' => 'i', 'value' => 1]
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
