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

        $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int')  : [];
        $faculty_authorities_id = isset($_POST['faculty_authorities_id']) ? sanitizeInput($_POST['faculty_authorities_id'], 'int')  : [];
        $faculty_dept_id = isset($_POST['faculty_dept_id']) ? sanitizeInput($_POST['faculty_dept_id'], 'int')  : [];
        $faculty_authorities_group_id = isset($_POST['faculty_authorities_group_id']) ? sanitizeInput($_POST['faculty_authorities_group_id'], 'int')  : [];

        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_id', 'type' => 'i', 'value' => json_encode($faculty_id)],
            ['name' => 'faculty_authorities_id', 'type' => 'i', 'value' => json_encode($faculty_authorities_id)],
            ['name' => 'faculty_dept_id', 'type' => 'i', 'value' => json_encode($faculty_dept_id)],
            ['name' => 'faculty_authorities_group_id', 'type' => 'i', 'value' => json_encode($faculty_authorities_group_id)]
        ];
        $result = callProcedure("update_pr_faculty_authorities_roles", $procedure_params);

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
