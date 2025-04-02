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
        $location_href = isset($_POST['location']) ? sanitizeInput($_POST['location'], 'string') : '';
        $params_procedures = [
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id]
        ];
        $result = callProcedure("fetch_pr_hod_view_student_allocation", $params_procedures);
        if ($result) {
            if (isset($result['particulars'][0]['status_code']) && $result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0]) && is_array($result['data'][0])) {
                    $data = $result['data'][0];
                    $transformedData = [];

                    foreach ($data as $entry) {
                        $key = $entry['year_of_study_id'] . '-' . $entry['section_id'];

                        $entry['student_name'] = str_replace('    ', '', $entry['student_name']);
                        if (!isset($transformedData[$key])) {
                            $transformedData[$key] = [
                                'year_of_study_id' => $entry['year_of_study_id'],
                                'year_of_study_title' => $entry['year_of_study_title'],
                                'section_id' => $entry['section_id'],
                                'section_title' => $entry['section_title']
                            ];
                        }

                        if (!empty($entry['student_name']) && !empty($entry['student_reg_number']) && !empty($entry['student_id'])) {
                            if (!isset($transformedData[$key]['students'])) {
                                $transformedData[$key]['students'] = [];
                            }
                            $transformedData[$key]['students'][] = [
                                'student_name' => $entry['student_name'],
                                'student_reg_number' => $entry['student_reg_number'],
                                'student_id' => $entry['student_id']
                            ];
                        }
                    }

                    $student_allocation_hod_view = array_values($transformedData);

                    echo json_encode([
                        'code' => $result['particulars'][0]['status_code'],
                        'status' => $result['particulars'][0]['status'],
                        'data' => $student_allocation_hod_view, // Use transformed data
                        'message' => $result['particulars'][0]['message'],
                    ]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message'] ?? 'An error occurred.']);
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
