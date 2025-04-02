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

        $address_house_number = isset($_POST['address_house_number']) ? sanitizeInput($_POST['address_house_number'], 'string') : '';
        $address_street = isset($_POST['address_street']) ? sanitizeInput($_POST['address_street'], 'string') : '';
        $address_locality = isset($_POST['address_locality']) ? sanitizeInput($_POST['address_locality'], 'string') : '';
        $address_pincode = isset($_POST['address_pincode']) ? sanitizeInput($_POST['address_pincode'], 'string') : '';
        $address_city = isset($_POST['address_city']) ? sanitizeInput($_POST['address_city'], 'string') : '';
        $address_district = isset($_POST['address_district']) ? sanitizeInput($_POST['address_district'], 'string') : '';
        $address_state = isset($_POST['address_state']) ? sanitizeInput($_POST['address_state'], 'string') : '';
        $address_country = isset($_POST['address_country']) ? sanitizeInput($_POST['address_country'], 'string') : '';



        if (empty($address_house_number)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your House Number.']);
            exit;
        }
        if (empty($address_locality)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your Locality.']);
            exit;
        }
        if (empty($address_street)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your Street.']);
            exit;
        }
        if (empty($address_pincode)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your Pincode.']);
            exit;
        }
        if (empty($address_city)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your City.']);
            exit;
        }
        if (empty($address_district)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your District.']);
            exit;
        }
        if (empty($address_state)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your State.']);
            exit;
        }
        if (empty($address_country)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please select your Country.']);
            exit;
        }

        $procedure_params = [
            ['name' => 'faculty_id', 'value' => $logged_user_id, 'type' => 'i'],
            ['name' => 'login_in_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'address_house_number', 'value' => $address_house_number, 'type' => 's'],
            ['name' => 'address_street', 'value' => $address_street, 'type' => 's'],
            ['name' => 'address_locality', 'value' => $address_locality, 'type' => 's'],
            ['name' => 'address_pincode', 'value' => $address_pincode, 'type' => 's'],
            ['name' => 'address_city', 'value' => $address_city, 'type' => 's'],
            ['name' => 'address_district', 'value' => $address_district, 'type' => 's'],
            ['name' => 'address_state', 'value' => $address_state, 'type' => 's'],
            ['name' => 'address_country', 'value' => $address_country, 'type' => 's'],

        ];
        $result = callProcedure("update_pr_faculty_personal_address_profile_info", $procedure_params);
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            exit;
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating Contact details.']);
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
