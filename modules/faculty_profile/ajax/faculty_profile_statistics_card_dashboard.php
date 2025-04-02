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
            ['name' => 'faculty_id', 'type' => 'i', 'value' => $faculty_id]
        ];
        $result = callProcedure("fetch_pr_faculty_profile_statistics_card_dashboard", $procedure_params);
        if ($result) {
            if ($result['data']) {
                $result_data = $result['data'];
                $data = [];
                if ($type == 1) {
                    if (isset($result_data[0][0]['active_authorities_count']) && isset($result_data[1][0]['active_class_advisors_count']) && isset($result_data[2][0]['active_teaching_faculty_role']) && isset($result_data[3][0]['active_non_teaching_faculty_role'])) {

                        $data = [
                            "authorities_count" => $result_data[0][0]['active_authorities_count'],
                            "class_advisors_count" => $result_data[1][0]['active_class_advisors_count'],
                            "teaching_faculty_count" => $result_data[2][0]['active_teaching_faculty_role'],
                            "non_teaching_faculty_count" => $result_data[3][0]['active_non_teaching_faculty_role'],
                            'type' => 1
                        ];
                    } else {
                        $data = [
                            "authorities_count" => 0,
                            "class_advisors_count" => 0,
                            "teaching_faculty_count" => 0,
                            "non_teaching_faculty_count" => 0,
                            'type' => 1
                        ];
                    }
                } elseif ($type == 2) {
                    if ($dept_id == 0) {
                        if (isset($result_data[0][0]['total_students']) && isset($result_data[1][0]['total_boys']) && isset($result_data[2][0]['total_girls']) && isset($result_data[3][0]['total_dropouts'])) {
                            $data = [
                                "total_students" => $result_data[0][0]['total_students'],
                                "total_boys" => $result_data[1][0]['total_boys'],
                                "total_girls" => $result_data[2][0]['total_girls'],
                                "total_dropouts" => $result_data[3][0]['total_dropouts'],
                                "total_mentees" => 0,
                                'type' => 2
                            ];
                        } else {
                            $data = [
                                "total_students" => 0,
                                "total_boys" => 0,
                                "total_girls" => 0,
                                "total_dropouts" => 0,
                                "total_mentees" => 0,
                                'type' => 2
                            ];
                        }
                    } else {
                        if (isset($result_data[0][0]['total_students']) && isset($result_data[1][0]['total_boys']) && isset($result_data[2][0]['total_girls']) && isset($result_data[3][0]['total_mentees'])) {
                            $data = [
                                "total_students" => $result_data[0][0]['total_students'],
                                "total_boys" => $result_data[1][0]['total_boys'],
                                "total_girls" => $result_data[2][0]['total_girls'],
                                "total_mentees" => $result_data[3][0]['total_mentees'],
                                "total_dropouts" => 0,
                                'type' => 2
                            ];
                        } else {
                            $data = [
                                "total_students" => 0,
                                "total_boys" => 0,
                                "total_girls" => 0,
                                "total_mentees" => 0,
                                "total_dropouts" => 0,
                                'type' => 2
                            ];
                        }
                    }
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data]);
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
