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

        $sslc_institution_name = isset($_POST['sslc_institution_name']) ? sanitizeInput($_POST['sslc_institution_name'], 'string') : '';
        $education_board = isset($_POST['education_board']) ? sanitizeInput($_POST['education_board'], 'int') : 0;
        $sslc_passed_out_year = isset($_POST['sslc_passed_out_year']) ? sanitizeInput($_POST['sslc_passed_out_year'], 'string') : '';
        $sslc_percentage = isset($_POST['sslc_percentage']) ? sanitizeInput($_POST['sslc_percentage'], 'string') : '';

        //hsc
        $hsc_institution_name = isset($_POST['hsc_institution_name']) ? sanitizeInput($_POST['hsc_institution_name'], 'string')  : '';
        $education_hsc_board = isset($_POST['education_hsc_board']) ? sanitizeInput($_POST['education_hsc_board'], 'int') : 0;
        $hsc_mark = isset($_POST['hsc_mark']) ? sanitizeInput($_POST['hsc_mark'], 'int') : 0;
        $sslc_mark = isset($_POST['sslc_mark']) ? sanitizeInput($_POST['sslc_mark'], 'int') : 0;
        $education_hsc_specialization = isset($_POST['education_hsc_specialization']) ? sanitizeInput($_POST['education_hsc_specialization'], 'int') : 0;
        $hsc_passed_out_year = isset($_POST['hsc_passed_out_year']) ? sanitizeInput($_POST['hsc_passed_out_year'], 'string') : '';
        $hsc_percentage = isset($_POST['hsc_percentage']) ? sanitizeInput($_POST['hsc_percentage'], 'string') : '';

        //chekc empty individual
        if (empty($hsc_institution_name)
        && empty($education_hsc_board)
        && empty($education_hsc_specialization)
        && empty($hsc_passed_out_year)
        && empty($hsc_percentage)
        && empty($sslc_institution_name)
        && empty($education_board)
        && empty($sslc_passed_out_year)
        && empty($sslc_percentage)
        && empty($sslc_mark)
        && empty($hsc_mark)) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You skipped the school Details']);
            exit;
        }
        if (empty($education_hsc_board)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your HSC education board.']);
            exit;
        }
        if (empty($education_hsc_specialization)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your HSC education specialization.']);
            exit;
        }
        if (empty($hsc_passed_out_year)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your HSC passed out year.']);
            exit;
        }
        if (empty($hsc_percentage)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your HSC percentage.']);
            exit;
        }

        //chekc empty individual
        if (empty($sslc_institution_name)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your SSLC institution name.']);
            exit;
        }
        if (empty($education_board)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your SSLC education board.']);
            exit;
        }
        if (empty($sslc_passed_out_year)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your SSLC passed out year.']);
            exit;
        }
        if (empty($sslc_percentage)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your SSLC percentage.']);
            exit;
        }
        if (empty($sslc_mark)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your SSLC mark.']);
            exit;
        }
        if (empty($hsc_mark)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your HSC mark.']);
            exit;
        }
// print_r($logged_login_id);
        $procedure_params = [
            ['name' => 'sslc institution name', 'type' => 's', 'value' => $sslc_institution_name],
            ['name' => 'education board', 'type' => 'i', 'value' => $education_board],
            ['name' => 'sslc passed out year', 'type' => 's', 'value' => $sslc_passed_out_year],
            ['name' => 'sslc percentage', 'type' => 's', 'value' => $sslc_percentage],
            ['name' => 'hsc institution name', 'type' => 's', 'value' => $hsc_institution_name],
            ['name' => 'education hsc board', 'type' => 'i', 'value' => $education_hsc_board],
            ['name' => 'education hsc specialization', 'type' => 'i', 'value' => $education_hsc_specialization],
            ['name' => 'hsc passed out year', 'type' => 's', 'value' => $hsc_passed_out_year],
            ['name' => 'hsc percentage', 'type' => 's', 'value' => $hsc_percentage],
            ['name' => 'sslc mark', 'type' => 's', 'value' => $sslc_mark],
            ['name' => 'hsc mark', 'type' => 's', 'value' => $hsc_mark],
            ['name' => 'existing student id', 'type' => 'i', 'value' => $existingAdmissionValue],
             ['name' => 'new student id', 'type' => 'i', 'value' => $logged_login_id],
        ];
        // print_r($procedure_params);
        $result = callProcedure("update_stu_admission_education_schoolings_profile_info", $procedure_params);
        // print_r($result);
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating personal details.']);
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
