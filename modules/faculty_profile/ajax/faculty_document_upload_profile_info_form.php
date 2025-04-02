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
        $faculty_resume_id = isset($_POST['faculty_resume_id']) ? sanitizeInput($_POST['faculty_resume_id'], 'int') : 0;
        $faculty_sslc_id = isset($_POST['faculty_sslc_id']) ? sanitizeInput($_POST['faculty_sslc_id'], 'int') : 0;
        $faculty_hsc_id = isset($_POST['faculty_hsc_id']) ? sanitizeInput($_POST['faculty_hsc_id'], 'int') : 0;
        $faculty_highest_qualification_id = isset($_POST['faculty_highest_qualification_id']) ? sanitizeInput($_POST['faculty_highest_qualification_id'], 'int') : 0;
        $faculty_experience_id = isset($_POST['previous_faculty_experience_id']) ? sanitizeInput($_POST['previous_faculty_experience_id'], 'int') : [];
        $faculty_profile_pic_id = isset($_POST['faculty_profile_pic_id']) ? sanitizeInput($_POST['faculty_profile_pic_id'], 'int') : 0;


        $previous_faculty_resume = isset($_POST['previous_faculty_resume']) ? sanitizeInput($_POST['previous_faculty_resume'], 'string') : '';
        $previous_faculty_sslc = isset($_POST['previous_faculty_sslc']) ? sanitizeInput($_POST['previous_faculty_sslc'], 'string') : '';
        $previous_faculty_hsc = isset($_POST['previous_faculty_hsc']) ? sanitizeInput($_POST['previous_faculty_hsc'], 'string') : '';
        $previous_faculty_highest_qualification = isset($_POST['previous_faculty_highest_qualification']) ? sanitizeInput($_POST['previous_faculty_highest_qualification'], 'string') : '';
        $previous_faculty_experience = isset($_POST['previous_faculty_experience']) ? sanitizeInput($_POST['previous_faculty_experience'], 'string') : '';
        $faculty_previous_profile_pic = isset($_POST['faculty_previous_profile_pic']) ? sanitizeInput($_POST['faculty_previous_profile_pic'], 'string') : '';

        $sslc_prefix = "";
        $hsc_prefix = "";
        $highest_qualification_prefix = "";
        $resume_prefix = "";
        $experience_prefix = "";
        $profile_pic_prefix = "";
        $sslc_link = "";
        $hsc_link = "";
        $highest_qualification_link = "";
        $resume_link = "";
        $profile_pic_link = "";
        $experience_link = [];

        $prefix_procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id]
        ];

       // print_r($_FILES['faculty_hsc_certificate']);
        $prefixes_result = callProcedure('fetch_pr_faculty_documents_prefixes', $prefix_procedure_params);
        if ($prefixes_result['particulars'][0]['status_code'] == 200) {
            $sslc_prefix = $prefixes_result['data'][0][0]['prefixes_title']  . '-' . $logged_account_code . '-';
            $hsc_prefix = $prefixes_result['data'][0][1]['prefixes_title']  . '-' . $logged_account_code . '-';
            $highest_qualification_prefix = $prefixes_result['data'][0][2]['prefixes_title']  . '-' . $logged_account_code . '-';
            $resume_prefix = $prefixes_result['data'][0][3]['prefixes_title']  . '-' . $logged_account_code . '-';
            $experience_prefix = $prefixes_result['data'][0][4]['prefixes_title']  . '-' . $logged_account_code . '-';
            $profile_pic_prefix = $prefixes_result['data'][0][5]['prefixes_title']  . '-' . $logged_account_code . '-';
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in fetching document prefixes.']);
            exit;
        }

        //handle files
        if ($faculty_resume_id != 0  && $_FILES['faculty_resume']['error'] == 4) {
            $resume_link = $previous_faculty_resume;
        } else {
            if (isset($_FILES['faculty_resume']) && $_FILES['faculty_resume']['error'] == 0) {
                $resume_result = uploadFile($_FILES['faculty_resume'], ROOT . '/global/uploads/faculty_resumes', $resume_prefix);
                if ($resume_result['status_code'] == 200) {
                    $resume_link = $resume_result['files'][0];
                }
            }
        }

        if ($faculty_sslc_id != 0  && $_FILES['faculty_sslc_certificate']['error'] == 4) {
            $sslc_link = $previous_faculty_sslc;
        } else {
            if (isset($_FILES['faculty_sslc_certificate']) && $_FILES['faculty_sslc_certificate']['error'] == 0) {
                $sslc_result = uploadFile($_FILES['faculty_sslc_certificate'], ROOT . '/global/uploads/faculty_sslc', $sslc_prefix);
                if ($sslc_result['status_code'] == 200) {
                    $sslc_link = $sslc_result['files'][0];
                }
            }
        }

        if ($faculty_hsc_id != 0  && $_FILES['faculty_hsc_certificate']['error'] == 4) {
            $hsc_link = $previous_faculty_hsc;
        } else {
            if (isset($_FILES['faculty_hsc_certificate']) && $_FILES['faculty_hsc_certificate']['error'] == 0) {
                $hsc_result = uploadFile($_FILES['faculty_hsc_certificate'], ROOT . '/global/uploads/faculty_hsc_certificate', $hsc_prefix);
                if ($hsc_result['status_code'] == 200) {
                    $hsc_link = $hsc_result['files'][0];
                }
            }
        }

        if ($faculty_highest_qualification_id != 0  && $_FILES['faculty_highest_qualification_certificate']['error'] == 4) {
            $highest_qualification_link = $previous_faculty_highest_qualification;
        } else {
            if (isset($_FILES['faculty_highest_qualification_certificate']) && $_FILES['faculty_highest_qualification_certificate']['error'] == 0) {
                $highest_qualification_result = uploadFile($_FILES['faculty_highest_qualification_certificate'], ROOT . '/global/uploads/faculty_highest_qualification', $highest_qualification_prefix);
                if ($highest_qualification_result['status_code'] == 200) {
                    $highest_qualification_link = $highest_qualification_result['files'][0];
                }
            }
        }
        if ($faculty_experience_id[0] != 0  && $_FILES['faculty_experience_certificate']['error'] == 4) {
            $experience_link = $previous_faculty_experience;
        } else {

            if (isset($_FILES['faculty_experience_certificate']) && !empty($_FILES['faculty_experience_certificate']['name'][0])) {

                foreach ($_FILES['faculty_experience_certificate']['name'] as $index => $fileName) {
                    if ($_FILES['faculty_experience_certificate']['error'][$index] == 0) {
                        // Create a single file array for each file
                        $fileArray = [
                            'name' => $_FILES['faculty_experience_certificate']['name'][$index],
                            'full_path' => $_FILES['faculty_experience_certificate']['full_path'][$index],
                            'type' => $_FILES['faculty_experience_certificate']['type'][$index],
                            'tmp_name' => $_FILES['faculty_experience_certificate']['tmp_name'][$index],
                            'error' => $_FILES['faculty_experience_certificate']['error'][$index],
                            'size' => $_FILES['faculty_experience_certificate']['size'][$index],
                        ];
                        // Upload each file individually
                        $experience_result = uploadFile($fileArray, ROOT . '/global/uploads/faculty_experience_certificate', $experience_prefix);

                        // Check if upload was successful
                        if ($experience_result['status_code'] == 200) {
                            $experience_link[] = $experience_result['files']; // Store link to successfully uploaded file
                        }
                    } else {
                        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in uploading experience certificate.']);
                        exit;
                    }
                }
            }
        }

        if ($faculty_profile_pic_id != 0 && $_FILES['faculty_profile_pic']['error'] == 4) {
            $profile_pic_link = $faculty_previous_profile_pic;
        } else {
            if (isset($_FILES['faculty_profile_pic'])  && $_FILES['faculty_profile_pic']['error'] == 0) {
                $profile_pic_result = uploadFile($_FILES['faculty_profile_pic'], ROOT . '/global/uploads/faculty_profile_pic', $profile_pic_prefix);
                if ($profile_pic_result['status_code'] == 200) {
                    $profile_pic_link = $profile_pic_result['files'][0];
                }
            }
        }
        $procedure_params = [

            ['name' => 'faculty id', 'type' => 'i', 'value' => $logged_user_id],
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'faculty_resume_id', 'type' => 'i', 'value' => $faculty_resume_id],
            ['name' => 'faculty_sslc_id', 'type' => 'i', 'value' => $faculty_sslc_id],
            ['name' => 'faculty_hsc_id', 'type' => 'i', 'value' => $faculty_hsc_id],
            ['name' => 'faculty_highest_qualification_id', 'type' => 'i', 'value' => $faculty_highest_qualification_id],
            ['name' => 'faculty_experience_id', 'type' => 'i', 'value' => json_encode($faculty_experience_id)],
            ['name' => 'sslc', 'type' => 's', 'value' => $sslc_link],
            ['name' => 'hsc', 'type' => 's', 'value' => $hsc_link],
            ['name' => 'highest_qualification', 'type' => 's', 'value' => $highest_qualification_link],
            ['name' => 'resume', 'type' => 's', 'value' => $resume_link],
            ['name' => 'experience', 'type' => 's', 'value' => json_encode($experience_link)],
            ['name' => 'profile pic', 'type' => 's', 'value' => $profile_pic_link],
            ['name' => 'profile pic id', 'type' => 'i', 'value' => $faculty_profile_pic_id]

        ];
        
         $result = callProcedure("update_pr_faculty_document_profile_info", $procedure_params);
        if ($result) {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
            $_SESSION['svcet_educnt_faculty_profile_status'] = 1;
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
