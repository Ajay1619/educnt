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

        $room_id = isset($_POST['room_id']) ? sanitizeInput($_POST['room_id'], 'int') : 0;
        $room_number = isset($_POST['room_number']) ? sanitizeInput($_POST['room_number'], 'string') : "";
        $room_name = isset($_POST['room_name']) ? sanitizeInput($_POST['room_name'], 'string') : "";
        $room_floor = isset($_POST['room_floor']) ? sanitizeInput($_POST['room_floor'], 'int') : 0;
        $room_category = isset($_POST['room_category']) ? sanitizeInput($_POST['room_category'], 'string') : "";
        $room_max_capacity = isset($_POST['room_max_capacity']) ? sanitizeInput($_POST['room_max_capacity'], 'string') : "";
        $room_type = isset($_POST['room_type']) ? sanitizeInput($_POST['room_type'], 'int') : 1;


        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => 0],
            ['name' => 'room_id', 'type' => 'i', 'value' => $room_id],
            ['name' => 'room_number', 'type' => 'i', 'value' => $room_number],
            ['name' => 'room_name', 'type' => 's', 'value' => $room_name],
            ['name' => 'room_floor', 'type' => 'i', 'value' => $room_floor],
            ['name' => 'room_category', 'type' => 's', 'value' => $room_category],
            ['name' => 'room_max_capacity', 'type' => 'i', 'value' => $room_max_capacity],
            ['name' => 'room_type', 'type' => 'i', 'value' => $room_type],

            ['name' => 'p_type', 'type' => 'i', 'value' => 1]
        ];
        $result = callProcedure("update_pr_stock_add_rooms", $params_procedures);
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
