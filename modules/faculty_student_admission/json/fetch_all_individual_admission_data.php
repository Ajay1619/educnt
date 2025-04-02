<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        
        $student_id = decrypt_data($_GET['student_id']);
        

        $student_id = isset($student_id) ? sanitizeInput($student_id, 'int') : 0;

        $procedure_params = [
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'user_id', 'value' => $student_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_faculty_individual_admission_data", $procedure_params);
      
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                $data = $result['data'];
                if ($data[0][0]['student_dob']) {
                    $data[0][0]['student_dob'] = date(DATE_FORMAT, strtotime($data[0][0]['student_dob']));
                    //calculate age with dob
                    $age = calculateAge($data[0][0]['student_dob']);
                    $data[0][0]['student_age'] = $age;
                }

                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data]);
                exit;
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
