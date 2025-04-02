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

        $first_name = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name'], 'string') : '';
        $middle_name = isset($_POST['middle_name']) ? sanitizeInput($_POST['middle_name'], 'string') : '';
        $last_name = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name'], 'string') : '';
        $initial = isset($_POST['initial']) ? sanitizeInput($_POST['initial'], 'string') : '';
        $salutation = isset($_POST['salutation']) ? sanitizeInput($_POST['salutation'], 'string') : '';
        $date_of_birth = isset($_POST['date_of_birth']) ? sanitizeInput($_POST['date_of_birth'], 'string') : '';
        $gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender'], 'int') : 0;
        $blood_group = isset($_POST['blood-group']) ? sanitizeInput($_POST['blood-group'], 'int') : 0;
        $aadhar_number = isset($_POST['aadhar_number']) ? sanitizeInput($_POST['aadhar_number'], 'string') : '';
        $religion = isset($_POST['religion']) ? sanitizeInput($_POST['religion'], 'int') : NULL;
        $caste = isset($_POST['caste']) ? sanitizeInput($_POST['caste'], 'int') : NULL;
        $community = isset($_POST['community']) ? sanitizeInput($_POST['community'], 'int') : NULL;
        $nationality = isset($_POST['nationality']) ? sanitizeInput($_POST['nationality'], 'int') : 0;
        $marital_status = isset($_POST['marital-status']) ? sanitizeInput($_POST['marital-status'], 'int') : 0;

        if (empty($first_name)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your first name.']);
            exit;
        }

        if (empty($initial)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your initial.']);
            exit;
        }
        if (empty($salutation)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your salutation.']);
            exit;
        }
        if (empty($date_of_birth)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your date of birth.']);
            exit;
        }
        if (empty($gender)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your Gender.']);
            exit;
        }
        if (empty($blood_group)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your Blood Group.']);
            exit;
        }
        if (empty($aadhar_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your Aadhar Number.']);
            exit;
        }



        if (empty($marital_status)) {

            $marital_status  = null;
        }
        if (empty($nationality)) {

            $nationality  = null;
        }
        if (empty($religion)) {

            $religion  = null;
        }
        if (empty($caste)) {

            $caste  = null;
        }
        if (empty($community)) {

            $community  = null;
        }
        $procedure_params = [
            ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_in_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'first_name', 'value' => $first_name, 'type' => 's'],
            ['name' => 'middle_name', 'value' => $middle_name, 'type' => 's'],
            ['name' => 'last_name', 'value' => $last_name, 'type' => 's'],
            ['name' => 'initial', 'value' => $initial, 'type' => 's'],
            ['name' => 'salutation', 'value' => $salutation, 'type' => 'i'],
            ['name' => 'date_of_birth', 'value' => date(DB_DATE_FORMAT, strtotime($date_of_birth)), 'type' => 's'],
            ['name' => 'gender', 'value' => $gender, 'type' => 'i'],
            ['name' => 'blood_group', 'value' => $blood_group, 'type' => 'i'],
            ['name' => 'aadhar_number', 'value' => $aadhar_number, 'type' => 's'],
            ['name' => 'religion', 'value' => $religion, 'type' => 'i'],
            ['name' => 'caste', 'value' => $caste, 'type' => 'i'],
            ['name' => 'community', 'value' => $community, 'type' => 'i'],
            ['name' => 'nationality', 'value' => $nationality, 'type' => 'i'],
            ['name' => 'marital_status', 'value' => $marital_status, 'type' => 'i']

        ];
        // print_r($procedure_params);
        $result = callProcedure("update_pr_faculty_personal_profile_info", $procedure_params);
    //    print_r($result);
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
