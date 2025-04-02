<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    $svcet_educnt_user_name = isset($_POST['svcet_educnt_user_name']) ? sanitizeInput($_POST['svcet_educnt_user_name'], 'string') : '';
    $svcet_educnt_password = isset($_POST['svcet_educnt_password']) ? sanitizeInput($_POST['svcet_educnt_password'], 'string') : '';

    //check error for each input and return error json encode
    if (empty($svcet_educnt_user_name)) {

        echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your username.']);
        exit;
    }
    if (empty($svcet_educnt_password)) {
        echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your password.']);
        exit;
    }
    $result = passwordVerify($svcet_educnt_password, $svcet_educnt_user_name, $logged_portal_type);
    if ($result) {
        if ($result['status_code'] == 200) {
            $_SESSION['svcet_educnt_faculty_first_name'] = $result['user_data']['first_name'];
            $_SESSION['svcet_educnt_faculty_middle_name'] = $result['user_data']['middle_name'];
            $_SESSION['svcet_educnt_faculty_last_name'] = $result['user_data']['last_name'];
            $_SESSION['svcet_educnt_faculty_initial'] = $result['user_data']['user_initial'];
            $_SESSION['svcet_educnt_faculty_designation'] = $result['user_data']['designation'];
            $_SESSION['svcet_educnt_faculty_portal_type'] = $result['user_data']['portal_type'];
            $_SESSION['svcet_educnt_faculty_account_code'] = $result['user_data']['account_prefix'] . $result['user_data']['account_code'];
            $_SESSION['svcet_educnt_faculty_role_id'] = $result['user_data']['role_id'];
            $_SESSION['svcet_educnt_faculty_account_username'] = $result['user_data']['account_username'];
            $_SESSION['svcet_educnt_faculty_login_id'] = $result['user_data']['login_id'];
            $_SESSION['svcet_educnt_faculty_account_id'] = $result['user_data']['account_id'];
            $_SESSION['svcet_educnt_faculty_user_id'] = $result['user_data']['user_id'];
            $_SESSION['svcet_educnt_faculty_profile_status'] = $result['user_data']['profile_status'];
            $_SESSION['svcet_educnt_faculty_salutation'] = $result['user_data']['faculty_salutation'];
            $_SESSION['svcet_educnt_faculty_dept_title'] = $result['user_data']['dept_title'];
            $_SESSION['svcet_educnt_faculty_dept_id'] = $result['user_data']['dept_id'];
            $_SESSION['svcet_educnt_faculty_dept_short_name'] = $result['user_data']['dept_short_name'];
            $_SESSION['svcet_educnt_faculty_profile_pic'] = $result['user_data']['profile_pic_path'];
            $_SESSION['routing'] = 'Faculty';
            $_SESSION['sparrow_faculty_csrf_token'] = generateCsrfToken();


            $_SESSION['svcet_educnt_faculty_page_access_data'] = $result['user_page_access_data'];
            $redirect_link = "";
            if ($result['user_data']['profile_status'] == 0) {
                $redirect_link = BASEPATH . '/faculty-profile?action=add&route=faculty&type=personal&tab=personal';
            } else if ($result['user_data']['profile_status'] == 1) {
                switch ($_SESSION['svcet_educnt_faculty_role_id']) {


                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                        $redirect_link = BASEPATH . '/faculty-profile?action=view&route=faculty&type=overall';
                        break;
                    case 7:
                        //use exam cell link before  uncommant
                         $redirect_link = BASEPATH . '/faculty-student-examination?action=view&route=faculty';
                        break;
                    case 8:
                        $redirect_link = BASEPATH . '/faculty-profile?action=view&route=faculty';
                        break;
                    case 9:
                        $redirect_link = BASEPATH . '/faculty-profile?action=view&route=faculty';
                        break;
                    case 10:
                    case 11:
                    case 12:
                        //use Dean - IQAC link before  uncommant
                        // $redirect_link = BASEPATH . '/faculty-profile?action=view&route=faculty';
                        break;
                    case 13:
                        $redirect_link = BASEPATH . '/faculty-student-admission?action=view&route=faculty&type=overall';
                        break;
                    case 14:
                        //use Placement cell link before  uncommant
                        // $redirect_link = BASEPATH . '/faculty-student-admission?action=view&route=faculty&type=overall';
                        break;

                    default:
                        $redirect_link = BASEPATH . '/faculty-profile?action=view&route=faculty&type=overall';
                        break;
                }
            }
            echo json_encode(['code' => 200, 'status' => $result['status'], 'message' => $result['message'], 'redirect_link' => $redirect_link]);
            exit;
        } else {
            echo json_encode(['code' => $result['status_code'], 'status' => $result['status'], 'message' => $result['message']]);
            exit;
        }
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
