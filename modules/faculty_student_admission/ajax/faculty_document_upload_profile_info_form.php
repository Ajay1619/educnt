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
        $student_sslc_id = isset($_POST['student_sslc_id']) ? sanitizeInput($_POST['student_sslc_id'], 'int') : 0;
        $student_hsc_id = isset($_POST['student_hsc_id']) ? sanitizeInput($_POST['student_hsc_id'], 'int') : 0;
        $student_highest_qualification_id = isset($_POST['student_highest_qualification_id']) ? sanitizeInput($_POST['student_highest_qualification_id'], 'int') : 0;
        $student_profile_pic_id = isset($_POST['student_profile_pic_id']) ? sanitizeInput($_POST['student_profile_pic_id'], 'int') : 0;
        $student_transfer_certificate_id = isset($_POST['student_transfer_certificate_id']) ? sanitizeInput($_POST['student_transfer_certificate_id'], 'int') : 0;
        $student_permanent_integrated_certificate_id = isset($_POST['student_permanent_integrated_certificate_id']) ? sanitizeInput($_POST['student_permanent_integrated_certificate_id'], 'int') : 0;
        $student_community_certificate_id = isset($_POST['student_community_certificate_id']) ? sanitizeInput($_POST['student_community_certificate_id'], 'int') : 0;
        $student_residence_certificate_id = isset($_POST['student_residence_certificate_id']) ? sanitizeInput($_POST['student_residence_certificate_id'], 'int') : 0;



        $previous_student_sslc = isset($_POST['previous_student_sslc']) ? sanitizeInput($_POST['previous_student_sslc'], 'string') : '';
        $previous_student_hsc = isset($_POST['previous_student_hsc']) ? sanitizeInput($_POST['previous_student_hsc'], 'string') : '';
        $previous_student_highest_qualification = isset($_POST['previous_student_highest_qualification']) ? sanitizeInput($_POST['previous_student_highest_qualification'], 'string') : '';
        $previous_student_permanent_integrated_certificate = isset($_POST['previous_student_permanent_integrated_certificate']) ? sanitizeInput($_POST['previous_student_permanent_integrated_certificate'], 'string') : '';
        $previous_student_community_certificate = isset($_POST['previous_student_community_certificate']) ? sanitizeInput($_POST['previous_student_community_certificate'], 'string') : '';
        $student_previous_profile_pic = isset($_POST['student_previous_profile_pic']) ? sanitizeInput($_POST['student_previous_profile_pic'], 'string') : '';
        $previous_student_transfer_certificate = isset($_POST['previous_student_transfer_certificate']) ? sanitizeInput($_POST['previous_student_transfer_certificate'], 'string') : '';
        $previous_student_residence_certificate = isset($_POST['previous_student_residence_certificate']) ? sanitizeInput($_POST['previous_student_residence_certificate'], 'string') : '';

      
        $sslc_prefix = "";
        $hsc_prefix = "";
        $highest_qualification_prefix = "";
        $permanent_integrated_certificate_prefix = "";
        $previous_student_transfer_certificate_prefix = "";
        $community_certificate_prefix = "";
        $student_residence_certificate_prefix = "";
        $profile_pic_prefix = "";

        $sslc_link = "";
        $hsc_link = "";
        $highest_qualification_link = "";
        $permanent_integrated_certificate_link = "";
        $previous_student_transfer_certificate_link = "";
        $community_certificate_link = "";
        $student_residence_certificate_link = "";
        $profile_pic_link = "";

        // $sslc_link = "";
        // $hsc_link = "";
        // $highest_qualification_link = "";
        // $resume_link = "";
        // $profile_pic_link = "";


        $prefix_procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id]
        ];
        $prefixes_result = callProcedure('fetch_pr_faculty_documents_prefixes', $prefix_procedure_params);
        //  print_r($prefixes_result);
        if ($prefixes_result['particulars'][0]['status_code'] == 200) {
            $sslc_prefix = $prefixes_result['data'][0][0]['prefixes_title']  . '-';
            $hsc_prefix = $prefixes_result['data'][0][1]['prefixes_title']  . '-';
            $highest_qualification_prefix = $prefixes_result['data'][0][2]['prefixes_title']  . '-';
            $permanent_integrated_certificate_prefix = 'PIC'  . '-';
            $previous_student_transfer_certificate_prefix = 'TC'  . '-';
            $community_certificate_prefix = 'Community-certificate'  . '-';
            $student_residence_certificate_prefix = 'Residence-Certificate'  . '-';
            $profile_pic_prefix = 'student-profile'  . '-';
            // $profile_pic_link = $prefixes_result['data'][0][5]['prefixes_title']  . '-';
            // $resume_prefix = $prefixes_result['data'][0][3]['prefixes_title']  . '-';
            // $experience_prefix = $prefixes_result['data'][0][4]['prefixes_title']  . '-';
            //  $profile_pic_prefix = $prefixes_result['data'][0][5]['prefixes_title']  . '-';
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in fetching document prefixes.']);
            exit;
        }
        // echo $logged_account_code;
        //handle files


        if ($student_sslc_id != 0 && $_FILES['student_sslc_certificate']['error'] == 4) {
            $sslc_link = $previous_student_sslc;
        } else {
            if ($_FILES['student_sslc_certificate']['error'] == 0) {
                $sslc_result = uploadFile($_FILES['student_sslc_certificate'], ROOT . '/global/uploads/student_sslc', $sslc_prefix);
                if ($sslc_result['status_code'] == 200) {
                    $sslc_link = $sslc_result['files'][0];
                }
            }
        }

        if ($student_hsc_id != 0 && $_FILES['student_hsc_certificate']['error'] == 4 ) {
            $hsc_link = $previous_student_hsc;
        } else {
            if ($_FILES['student_hsc_certificate']['error'] == 0) {
                $hsc_result = uploadFile($_FILES['student_hsc_certificate'], ROOT . '/global/uploads/student_hsc_certificate', $hsc_prefix);
                if ($hsc_result['status_code'] == 200) {
                    $hsc_link = $hsc_result['files'][0];
                }
            }
        }

        if ($student_highest_qualification_id != 0 && $_FILES['student_highest_qualification_certificate']['error'] == 4 ) {
            $highest_qualification_link = $previous_student_highest_qualification;
        } else {
            if ($_FILES['student_highest_qualification_certificate']['error'] == 0) {
                $highest_qualification_result = uploadFile($_FILES['student_highest_qualification_certificate'], ROOT . '/global/uploads/student_highest_qualification', $highest_qualification_prefix);
                if ($highest_qualification_result['status_code'] == 200) {
                    $highest_qualification_link = $highest_qualification_result['files'][0];
                }
            }
        }

        // Handle Transfer Certificate Upload
        if ($student_transfer_certificate_id != 0 && $_FILES['student_transfer_certificate']['error'] == 4 ) {
            $previous_student_transfer_certificate_link = $previous_student_transfer_certificate;
        } else {
            if ($_FILES['student_transfer_certificate']['error'] == 0) {
                $tc_result = uploadFile($_FILES['student_transfer_certificate'], ROOT . '/global/uploads/student_transfer_certificate', $previous_student_transfer_certificate_prefix);
                if ($tc_result['status_code'] == 200) {
                    $previous_student_transfer_certificate_link = $tc_result['files'][0];
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error uploading the Transfer Certificate.']);
                    exit;
                }
            }
        }

        if ($student_permanent_integrated_certificate_id != 0 && $_FILES['student_permanent_integrated_certificate']['error'] == 4 ) {
            $permanent_integrated_certificate_link = $previous_student_permanent_integrated_certificate;
        } else {
            if ($_FILES['student_permanent_integrated_certificate']['error'] == 0) {
                $permanent_integrated_certificate_result = uploadFile($_FILES['student_permanent_integrated_certificate'], ROOT . '/global/uploads/student_permanent_integrated_certificate', $permanent_integrated_certificate_prefix);
                if ($permanent_integrated_certificate_result['status_code'] == 200) {
                    $permanent_integrated_certificate_link = $permanent_integrated_certificate_result['files'][0];
                }
            }
        }

        if ($student_community_certificate_id != 0 && $_FILES['student_community_certificate']['error'] == 4 ) {
            $community_certificate_link = $previous_student_community_certificate;
        } else {
            if ($_FILES['student_community_certificate']['error'] == 0) {
                $community_certificate_result = uploadFile($_FILES['student_community_certificate'], ROOT . '/global/uploads/student_community_certificate', $community_certificate_prefix);
                if ($community_certificate_result['status_code'] == 200) {
                    $community_certificate_link = $community_certificate_result['files'][0];
                }
            }
        }

        if ($student_residence_certificate_id != 0 && $_FILES['student_residence_certificate']['error'] == 4) {
            $student_residence_certificate_link = $previous_student_residence_certificate;
        } else {
            if ($_FILES['student_residence_certificate']['error'] == 0) {
                $student_residence_certificate_result = uploadFile($_FILES['student_residence_certificate'], ROOT . '/global/uploads/student_residence_certificate', $student_residence_certificate_prefix);
                if ($student_residence_certificate_result['status_code'] == 200) {
                    $student_residence_certificate_link = $student_residence_certificate_result['files'][0];
                }
            }
        }

        if ($student_profile_pic_id != 0 && $_FILES['student_profile_pic']['error'] == 4) {
            $profile_pic_link = $student_previous_profile_pic;
            
        } else {
            
            if ($_FILES['student_profile_pic']['error'] == 0) {
                $profile_pic_result = uploadFile($_FILES['student_profile_pic'], ROOT . '/global/uploads/student_profile_pic', $profile_pic_prefix);
                if ($profile_pic_result['status_code'] == 200) {
                    $profile_pic_link = $profile_pic_result['files'][0];
                }
            }
        }
        $procedure_params = [
            ['name' => 'student_id', 'type' => 'i', 'value' => $existingAdmissionValue],
            ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'student_sslc_id', 'type' => 'i', 'value' => $student_sslc_id],
            ['name' => 'student_hsc_id', 'type' => 'i', 'value' => $student_hsc_id],
            ['name' => 'student_highest_qualification_id', 'type' => 'i', 'value' => $student_highest_qualification_id],
            ['name' => 'student_transfer_certificate_id', 'type' => 'i', 'value' => $student_transfer_certificate_id],
            ['name' => 'student_permanent_integrated_certificate_id', 'type' => 'i', 'value' => $student_permanent_integrated_certificate_id],
            ['name' => 'student_community_certificate_id', 'type' => 'i', 'value' => $student_community_certificate_id],
            ['name' => 'student_residence_certificate_id', 'type' => 'i', 'value' => $student_residence_certificate_id],
            ['name' => 'student_profile_pic_id', 'type' => 'i', 'value' => $student_profile_pic_id],
            ['name' => 'sslc', 'type' => 's', 'value' => $sslc_link],
            ['name' => 'hsc', 'type' => 's', 'value' => $hsc_link],
            ['name' => 'highest_qualification', 'type' => 's', 'value' => $highest_qualification_link],
            ['name' => 'transfer_certificate', 'type' => 's', 'value' => $previous_student_transfer_certificate_link],
            ['name' => 'permanent_integrated_certificate', 'type' => 's', 'value' => $permanent_integrated_certificate_link],
            ['name' => 'community_certificate', 'type' => 's', 'value' => $community_certificate_link],
            ['name' => 'residence_certificate', 'type' => 's', 'value' => $student_residence_certificate_link],
            ['name' => 'profile_pic', 'type' => 's', 'value' => $profile_pic_link]
        ];
         $result = callProcedure("update_pr_student_document_profile_info", $procedure_params);

        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            // $_SESSION['svcet_educnt_student_profile_status'] = 1;
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
