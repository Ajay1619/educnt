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
        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;

        $faculty_id = !in_array($logged_role_id, $main_roles) ? $logged_user_id : 0;
        $procedure_params = [
            ['name' => 'dept_id', 'value' => $dept_id, 'type' => 'i'],
            ['name' => 'year_of_study_id', 'value' => $year_of_study_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'faculty_id', 'value' => $faculty_id, 'type' => 'i'],
        ];
        $result = callProcedure("fetch_pr_class_advisors", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if ($result['data']) {
                    $roles_data = $result['data'][0];
                    if (isset($roles_data[0]['effective_from'])) {
                        foreach ($roles_data as $key => $value) {
                            $roles_data[$key]['effective_from'] = date(DATE_FORMAT, strtotime($value['effective_from']));
                            $roles_data[$key]['effective_to'] = empty($value['effective_to']) ? 'Till Date' : date(DATE_FORMAT, strtotime($value['effective_to']));
                        }
                    }

                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'roles_data' => $roles_data]);
                    exit;
                } else {
                    echo json_encode(['code' => 200, 'status' => 'warning', 'message' => 'No data found with your Filter.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
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
