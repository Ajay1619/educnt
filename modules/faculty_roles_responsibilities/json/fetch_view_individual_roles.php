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
        $role_id = isset($_POST['role_id']) ? sanitizeInput($_POST['role_id'], 'int') : null;
        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : null;

        $fetch_type = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int') : null;
        // Assign fetch_type based on $logged_role_Id
        $faculty_id = !in_array($logged_role_id, $main_roles) ? sanitizeInput($_POST['faculty_id'], 'int') : null;
        $fetch_type = in_array($logged_role_id, $main_roles) ? 1 : 2;
        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'faculty_id', 'value' => $faculty_id, 'type' => 'i'],
            ['name' => 'dept_id', 'value' => $dept_id, 'type' => 'i'],
            ['name' => 'role_id', 'value' => $role_id, 'type' => 'i'],
            ['name' => 'fetch_type', 'value' => $fetch_type, 'type' => 'i'],
        ];
        $result = callProcedure("fetch_pr_view_roles", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if ($result['data']) {
                    $roles_data = $result['data'][0];
                    foreach ($roles_data as $key => $value) {
                        // Format effective_from date
                        $roles_data[$key]['effective_from'] = date(DATE_FORMAT, strtotime($value['effective_from']));

                        // Format effective_to date or set 'Till Date' if null or empty
                        if (!empty($value['effective_to'])) {
                            $roles_data[$key]['effective_to'] = date(DATE_FORMAT, strtotime($value['effective_to']));
                        } else {
                            $roles_data[$key]['effective_to'] = 'Till Date';
                        }

                        // Assign role name based on committee_role value
                        switch ($value['committee_role']) {
                            case 1:
                                $roles_data[$key]['committee_role'] = 'Head';
                                break;
                            case 2:
                                $roles_data[$key]['committee_role'] = 'Co Ordinator';
                                break;
                            case 3:
                                $roles_data[$key]['committee_role'] = 'Associate Co Ordinator';
                                break;
                            case 4:
                                $roles_data[$key]['committee_role'] = 'Member';
                                break;
                            default:
                                $roles_data[$key]['committee_role'] = 'Unknown Role';
                                break;
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
