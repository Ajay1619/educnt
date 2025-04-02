<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX POST request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    // $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    try {
        // Validate and sanitize address form data
        $existing_id = isset($_POST['admission_student_existing']) ? sanitizeInput($_POST['admission_student_existing']) : 0;
        $address_pincode = isset($_POST['address_pincode']) ? sanitizeInput($_POST['address_pincode']) : '';
        $house_number = isset($_POST['address_house_number']) ? sanitizeInput($_POST['address_house_number'], 'string') : '';
        $street = isset($_POST['address_street']) ? sanitizeInput($_POST['address_street'], 'string') : '';
        $locality = isset($_POST['address_locality']) ? sanitizeInput($_POST['address_locality'], 'string') : '';
        $city = isset($_POST['address_city']) ? sanitizeInput($_POST['address_city'], 'string') : '';
        $district = isset($_POST['address_district']) ? sanitizeInput($_POST['address_district'], 'string') : '';
        $state = isset($_POST['address_state']) ? sanitizeInput($_POST['address_state'], 'string') : '';
        $country = isset($_POST['address_country']) ? sanitizeInput($_POST['address_country'], 'string') : '';
        
        if (empty($address_pincode) && empty($house_number)) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You skiped the Address Details.']);
            exit;
        }
        // Basic validation for required fields
        if ($address_pincode == 0 || $address_pincode == ""  ) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You Skiped Address Information!']);
            exit;
        } 

        // Parameters for the stored procedure
        $procedure_params = [
            ['name' => 'existing_id', 'value' => $existingAdmissionValue, 'type' => 0],
            ['name' => 'address_pincode', 'value' => $address_pincode, 'type' => 's'],
            ['name' => 'house_number', 'value' => $house_number, 'type' => 's'],
            ['name' => 'street', 'value' => $street, 'type' => 's'],
            ['name' => 'locality', 'value' => $locality, 'type' => 's'],
            ['name' => 'city', 'value' => $city, 'type' => 's'],
            ['name' => 'district', 'value' => $district, 'type' => 's'],
            ['name' => 'state', 'value' => $state, 'type' => 's'],
            ['name' => 'country', 'value' => $country, 'type' => 's']
        ];
        // print_r($procedure_params);
        // Call the procedure to insert address details
        $result = callProcedure("insert_student_address_details", $procedure_params);
// print_r($result);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'existing_student_id' => $result['particulars'][0]['existing_student_id']]);
                $_SESSION['admission_student_existing'] = $result['particulars'][0]['existing_student_id'];
                // echo $_SESSION['admission_student_existing'];
                // $logged_profile_status = isset($_SESSION['svcet_educnt_faculty_profile_status']) ? $_SESSION['svcet_educnt_faculty_profile_status'] : 0;
                exit;
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'existing_student_id' => $result['particulars'][0]['existing_student_id']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
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
