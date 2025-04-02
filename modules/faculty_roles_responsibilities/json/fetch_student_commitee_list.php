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
        $role_id = isset($_POST['role_id']) ? sanitizeInput($_POST['role_id'], 'int') : 0;
        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'dept_id', 'value' => $logged_dept_id, 'type' => 'i'],
            ['name' => 'role_id', 'value' => $role_id, 'type' => 'i'],
        ];

        $result = callProcedure("fetch_pr_student_commitee_list", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if (isset($result['data'][0])) {

                    $commitee_data = $result['data'][0];;
                    $commitee_student_data = [];
                    if (isset($result['data'][1])) {

                        $commitee_student_data = $result['data'][1];
                        foreach ($commitee_student_data as $key => $value) {
                            $commitee_student_data[$key]['effective_from'] = empty(date(DATE_FORMAT, strtotime($value['effective_from'])));
                            $commitee_student_data[$key]['effective_to'] = empty($value['effective_to']) ? 'Till Date' : date(DATE_FORMAT, strtotime($value['effective_to']));
                        }
                        echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'commitee_data' => $commitee_data, 'commitee_student_data' => $commitee_student_data]);
                        exit;
                    } else {
                        echo json_encode(['code' => 200, 'status' => 'warning', 'commitee_data' => $commitee_data, 'message' => 'No data found with your Filter.']);
                        exit;
                    }
                } else {
                    echo json_encode(['code' => 200, 'status' => 'warning', 'message' => 'No data found with your Filter.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 200, 'status' => 'warning', 'message' => 'No data found with your Filter.']);
            exit;
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
