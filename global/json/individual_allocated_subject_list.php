<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    try {
        $user_id = 0;
        $dept_id = 0;
        if (in_array($logged_role_id, $tertiary_roles)) {
            $user_id = $logged_user_id;
        } else {
            if (in_array($logged_role_id, $primary_roles)) {
                $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
            } else {
                $dept_id = $logged_dept_id;
            }
        }

        $procedure_params = [

            ['name' => 'user_id', 'value' => $user_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'dept_id', 'value' => $dept_id, 'type' => 'i']
        ];
        $result = callProcedure('fetch_pr_individual_allocated_subject_list', $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if (isset($result['data'][0]) && is_array($result['data'][0])) {
                    $data = $result['data'][0];
                    $subject_list = array_map(function ($item) {
                        return [
                            "value" => $item["faculty_subjects_id"] ?? null,
                            "title" => $item["subject_name"] ?? null,
                            "code" => $item["section_title"] . "-Section" ?? null
                        ];
                    }, $data);


                    echo json_encode([
                        'code' => $result['particulars'][0]['status_code'],
                        'status' => $result['particulars'][0]['status'],
                        'message' => $result['particulars'][0]['message'],
                        'data' => $data,
                        'dropdown_data' => $subject_list
                    ]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message']
                ]);
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
