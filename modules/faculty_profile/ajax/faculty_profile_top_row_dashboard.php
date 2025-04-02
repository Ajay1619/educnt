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
        $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int') : 1;
        $dept_id = !in_array($logged_role_id, $tertiary_roles) ? $logged_dept_id : 0;
        $faculty_id = in_array($logged_role_id, $tertiary_roles) ? $logged_user_id : 0;
        $procedure_params = [
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'type', 'type' => 'i', 'value' => $type],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $dept_id],
            ['name' => 'faculty_id', 'type' => 'i', 'value' => $faculty_id],
        ];
        $result = callProcedure("fetch_pr_faculty_profile_top_row_dashboard", $procedure_params);
        if ($result) {
            if ($result['data']) {
                $result_data = $result['data'];

                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $result_data]);
                exit;
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in Statistics Fetching Data.']);
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
