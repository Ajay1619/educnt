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

        if (in_array($logged_role_id, $main_roles)) {
            $procedure_params = [
                ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
                ['name' => 'faculty_id', 'value' => 0, 'type' => 'i'],
                ['name' => 'fetch_type', 'value' => 1, 'type' => 'i'],
            ];

            $result = callProcedure("fetch_pr_faculty_authorities", $procedure_params);

            if ($result) {
                if ($result['particulars'][0]['status_code'] == 200) {
                    if ($result['data']) {
                        $roles_data = $result['data'][0];
                        usort($roles_data, function ($a, $b) {
                            // Define the custom order for faculty_authorities_group_id
                            $group_order = [1, 2, 3, 5, 6, 7, 8, 9, 4];

                            // First, compare by faculty_authorities_group_id according to the custom order
                            $group_compare = array_search($a['faculty_authorities_group_id'], $group_order)
                                - array_search($b['faculty_authorities_group_id'], $group_order);

                            if ($group_compare !== 0) {
                                return $group_compare; // Return if they are not equal
                            }

                            // If faculty_authorities_group_id is the same, sort by faculty_id (with faculty_id first)
                            if ($a['faculty_id'] == null && $b['faculty_id'] !== null) {
                                return 1; // $a should come after $b (move entries with no faculty_id to the end)
                            }
                            if ($a['faculty_id'] !== null && $b['faculty_id'] == null) {
                                return -1; // $a should come before $b (move entries with faculty_id to the front)
                            }

                            return 0; // If both have faculty_id or neither, no change in order
                        });
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
            } else {
                echo json_encode(['code' => 200, 'status' => 'warning', 'message' => 'No data found with your Filter.']);
                exit;
            }
        }
        // individual authorities view
        else {

            $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int')  : null;
            $procedure_params = [
                ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
                ['name' => 'faculty_id', 'value' => $faculty_id, 'type' => 'i'],
                ['name' => 'fetch_type', 'value' => 2, 'type' => 'i'],
            ];

            $result = callProcedure("fetch_pr_faculty_authorities", $procedure_params);
            if ($result) {
                if ($result['particulars'][0]['status_code'] == 200) {
                    if ($result['data']) {
                        $roles_data = $result['data'][0];
                        foreach ($roles_data as $key => $value) {
                            if ($value['faculty_full_name'] = '  ') {
                                $roles_data[$key]['faculty_full_name'] = '';
                            }
                            $roles_data[$key]['effective_from'] = empty($value['effective_from']) ? '' : date(DATE_FORMAT, strtotime($value['effective_from']));
                            $roles_data[$key]['effective_to'] = empty($value['effective_to']) ? 'Till Date' : date(DATE_FORMAT, strtotime($value['effective_to']));
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
            } else {
                echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No data found with your Filter.']);
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
