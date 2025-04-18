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

        $room_id = isset($_POST['room_id']) ? sanitizeInput($_POST['room_id'], 'int') : 0;
        $item_name = isset($data['item_name']) ? sanitizeInput($data['item_name'], 'string') : "";
        $unit_of_measure = isset($data['unit_of_measure']) ? sanitizeInput($data['unit_of_measure'], 'string') : "";
        $item_quantity = isset($data['item_quantity']) ? sanitizeInput($data['item_quantity'], 'string') : "";
        $item_note = isset($data['item_note']) ? sanitizeInput($data['item_note'], 'string') : "";


        $params_procedures = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'room_id', 'type' => 'i', 'value' => $room_id],
            ['name' => 'item_id', 'type' => 'i', 'value' => 0],
            ['name' => 'item_name', 'type' => 's', 'value' => $item_name],
            ['name' => 'unit_of_measure', 'type' => 's', 'value' => $unit_of_measure],
            ['name' => 'item_quantity', 'type' => 's', 'value' => $item_quantity],
            ['name' => 'item_note', 'type' => 's', 'value' => $item_note],

            ['name' => 'p_type', 'type' => 'i', 'value' => 1]
        ];

        $result = callProcedure("update_pr_update_product_dept_room", $params_procedures);
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
