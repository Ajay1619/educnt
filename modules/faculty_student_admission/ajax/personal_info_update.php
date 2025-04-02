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
        try {
            // Validate form data
            // $existing_id = isset($_POST['admission_student_existing']) ? sanitizeInput($_POST['admission_student_existing']) : 0;
            $firstname = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name'], 'string') : '';
            $middlename = isset($_POST['middle_name']) ? sanitizeInput($_POST['middle_name'], 'string') : '';
            $lastname = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name'], 'string') : '';
            $initial = isset($_POST['initial']) ? sanitizeInput($_POST['initial'], 'string') : '';
            $dob = isset($_POST['date_of_birth']) ? sanitizeInput($_POST['date_of_birth'], 'string') : '';
            $gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender'], 'int') : 0;
            $aadhar = isset($_POST['aadhar_number']) ? sanitizeInput($_POST['aadhar_number'], 'string') : '';
            $religion = isset($_POST['religion']) ? sanitizeInput($_POST['religion'], 'int') : 0;
            $caste = isset($_POST['caste']) ? sanitizeInput($_POST['caste'], 'int') : 0;
            $community = isset($_POST['community']) ? sanitizeInput($_POST['community'], 'int') : 0;
            $nationality = isset($_POST['nationality']) ? sanitizeInput($_POST['nationality'], 'int') : 0;
            $blood_group = isset($_POST['blood-group']) ? sanitizeInput($_POST['blood-group'], 'int') : 0;
            $marital_status = isset($_POST['marital_status']) ? sanitizeInput($_POST['marital_status'], 'int') : 0;
           
            $datedob =  date("Y-m-d", strtotime($dob));
       


            if (empty($firstname)) {
                echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your firstname.']);
                exit;
            }
          
            if (empty($initial)) {
                echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your initial.']);
                exit;
            }
            if (empty($datedob)) {
                echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your date of birth.']);
                exit;
            }
            if (empty($gender)) {
                echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your gender.']);
                exit;
            }
            
            if (empty($blood_group)) {
                $blood_group = null;
            }
            if (empty($religion)) {
                $religion = null;
            }
            if (empty($caste)) {
                $caste = null;
            }
            if (empty($nationality)) {
                $nationality = null;
            }
            if (empty($marital_status)) {
                $marital_status = null;
            }
            if (empty($community)) {
                $community = null;
            }
           
           
            
            
           
            
            
            
            $procedure_params = [
                ['name' => 'existing_id', 'value' => $existingAdmissionValue, 'type' => 'i'],
                ['name' => 'first_name', 'value' => $firstname, 'type' => 's'],
                ['name' => 'middle_name', 'value' => $middlename, 'type' => 's'],
                ['name' => 'last_name', 'value' => $lastname, 'type' => 's'],
                ['name' => 'name_initial', 'value' => $initial, 'type' => 's'],
                ['name' => 'dob', 'value' => $datedob, 'type' => 's'],
                ['name' => 'gender', 'value' => $gender, 'type' => 'i'],
                ['name' => 'aadhar number', 'value' => $aadhar, 'type' => 's'],
                ['name' => 'religion', 'value' => $religion, 'type' => 'i'],
                ['name' => 'caste', 'value' => $caste, 'type' => 'i'],
                ['name' => 'community', 'value' => $community, 'type' => 'i'],
                ['name' => 'nationality', 'value' => $nationality, 'type' => 'i'],
                ['name' => 'blood_group', 'value' => $blood_group, 'type' => 'i'],
                ['name' => 'marital_status', 'value' => $marital_status, 'type' => 'i'],
            ];
             
            $result = callProcedure("insert_stu_create_addmission_profile", $procedure_params);
            //    print_rresult['particulars'][0]['existing_student_id']);
            //  print_r($procedure_params);
            if ($result) {
                if ($result['particulars'][0]['status_code'] == 200) {
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                    $_SESSION['admission_student_existing'] = $result['particulars'][0]['existing_student_id'];
                    //  echo $_SESSION['admission_student_existing'];
                    // $logged_profile_status = isset($_SESSION['svcet_educnt_faculty_profile_status']) ? $_SESSION['svcet_educnt_faculty_profile_status'] : 0;
                    exit;
                } else {
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                    exit;
                }
            } else {
                echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
                exit;
            }
        }catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }
