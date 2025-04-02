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
        $academic_batch_id = isset($_POST['academic_batch_id']) ? sanitizeInput($_POST['academic_batch_id'], 'int') : '';
        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : '';

        $params_procedures = [
            ['name' => 'academic_batch_id', 'type' => 'i', 'value' => $academic_batch_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $dept_id],
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id]
        ];
        $result = callProcedure("fetch_pr_year_of_study_and_section_with_academic_batch", $params_procedures);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0])) {
                    $data = $result['data'][0];
                    $year_of_study_data = [
                        'year_of_study_id' => $data[0]['year_of_study_id'],
                        'year_of_study_title' => $data[0]['year_of_study_title']
                    ];
                    $section_data = [];
                    foreach ($data as $item) {
                        $section_data[] = [
                            "title" => $item["section_title"],
                            "value" => $item["section_id"]
                        ];
                    }
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'year_of_study_data' => $year_of_study_data, 'section_data' => $section_data, 'message' => $result['particulars'][0]['message']]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
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
