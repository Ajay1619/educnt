<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $value = isset($_POST['value']) ? $_POST['value'] : '';

    if ($name == 'first_name' || $name == 'middle_name' || $name == 'last_name' || $name == 'initial'|| $name == 'guardian_name'|| $name == 'mother_name'|| $name == 'father_name') {
        $name = capitalizeFirstLetter($value);
        echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Capitalized the Input.', 'data' => $name]);
    }

    if ($name == 'address_street' || $name == 'address_locality' || $name == 'address_city' || $name == 'address_district' || $name == 'address_state' || $name == 'address_country' || $name == 'sslc_institution_name' || $name == 'hsc_institution_name' || $name == 'degree_institution_name[]' || $name == 'experience_designation[]' || $name == 'experience_industry_department[]' || $name == 'experience_industry_name[]') {
        $address = ucwords($value, " ");
        echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Capitalized the Input.', 'data' => $address]);
    }

    if ($name == 'aadhar_number') {
        $aadhar_number = formatAndValidateAadharNumber($value);
        if ($aadhar_number['status_code'] !== 200) {
            echo json_encode(['code' => $aadhar_number['status_code'], 'status' => $aadhar_number['status'], 'message' => $aadhar_number['message']]);
            exit;
        }
        echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Aadhar Number is valid.', 'data' => $aadhar_number['formatted_number']]);
    }
    if ($name == 'official_mail_id' || $name == 'mail_id') {
        $official_mail_id = isEmail($value);
        if ($official_mail_id == true) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => "Mail Id is valid.", 'data' => $value]);
            exit;
        } else {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => "Please Enter a Valid Mail ID."]);
            exit;
        }
    }
    if ($name == "phone_number" || $name == "alt_phone_number" || $name == "whatsapp_number") {
        $mobile_number = isPhoneNumber($value);
        if ($mobile_number == true) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => "Valid Mobile NUmber", 'data' => $value]);
            exit;
        } else {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => "Please Enter a Valid Mobile Number."]);
            exit;
        }
    }
    if ($name == 'sslc_passed_out_year' || $name == 'hsc_passed_out_year' || $name == 'degree_passed_out_year[]') {
        $sslc_passed_out_year = isYear($value);
        if ($sslc_passed_out_year == true) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => "Valid Year"]);
            exit;
        } else {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => "Please Enter a Valid Year."]);
            exit;
        }
    }
    if ($name == 'sslc_percentage' || $name == 'hsc_percentage' || $name == 'degree_percentage[]'  || $name == 'degree_cgpa[]') {
        $sslc_percentage = isPercentage($value);
        if ($sslc_percentage == true) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => "Valid Percentage"]);
            exit;
        } else {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => "Please Enter a Valid Percentage without % symbol."]);
            exit;
        }
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
