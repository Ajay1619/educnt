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
        $result = callProcedure("fetch_pr_hod_view_subject_allocation", $params_procedures);
        if (isset($result['data'][0])) {
            $data = $result['data'][0];
            $subject_allocation_hod_view = [];

            foreach ($data as $entry) {
                $entry['faculty_name'] = str_replace('    ', '', $entry['faculty_name']);
                $key = $entry['year_of_study_id'] . '-' . $entry['section_id'];

                // Check if subject_name, faculty_name, and room_name are all empty
                $isDetailsEmpty = empty($entry['subject_name']) && empty($entry['faculty_name']) && empty($entry['room_name']);

                // If the key already exists
                if (isset($subject_allocation_hod_view[$key])) {
                    // Only add details if they are not empty
                    if (!$isDetailsEmpty) {
                        $subject_allocation_hod_view[$key]['details'][] = [
                            'subject_name' => $entry['subject_name'],
                            'faculty_name' => $entry['faculty_name'],
                            'room_name' => $entry['room_name'],
                        ];
                    }
                } else {
                    // Initialize the array with or without details based on the condition
                    $subject_allocation_hod_view[$key] = [
                        'year_of_study_id' => $entry['year_of_study_id'],
                        'year_of_study_title' => $entry['year_of_study_title'],
                        'section_id' => $entry['section_id'],
                        'section_title' => $entry['section_title'],
                        'details' => !$isDetailsEmpty ? [[
                            'subject_name' => $entry['subject_name'],
                            'faculty_name' => $entry['faculty_name'],
                            'room_name' => $entry['room_name'],
                        ]] : [],
                    ];
                }
            }



            // Reindex the array to use numeric keys
            $subject_allocation_hod_view = array_values($subject_allocation_hod_view);

            echo json_encode([
                'code' => $result['particulars'][0]['status_code'],
                'status' => $result['particulars'][0]['status'],
                'data' => $subject_allocation_hod_view,
                'message' => $result['particulars'][0]['message'],
            ]);
            exit;
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
