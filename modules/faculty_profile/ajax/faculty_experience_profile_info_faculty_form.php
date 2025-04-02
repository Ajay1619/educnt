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

        //get experience inputs
        $experience_id = isset($_POST['experience_id']) ? sanitizeInput($_POST['experience_id'], 'int') : '';
        $field_of_experience = isset($_POST['field_of_experience']) ? sanitizeInput($_POST['field_of_experience'], 'int') : '';
        $experience_industry_name = isset($_POST['experience_industry_name']) ? sanitizeInput($_POST['experience_industry_name'], 'string') : '';
        $experience_designation = isset($_POST['experience_designation']) ? sanitizeInput($_POST['experience_designation'], 'string') : '';
        $experience_industry_department = isset($_POST['experience_industry_department']) ? sanitizeInput($_POST['experience_industry_department'], 'string') : '';
        $experience_industry_start_date = isset($_POST['experience_industry_start_date']) ? sanitizeInput($_POST['experience_industry_start_date'], 'string') : '';
        $experience_industry_end_date = isset($_POST['experience_industry_end_date']) ? sanitizeInput($_POST['experience_industry_end_date'], 'string') : '';

        //chekc empty individual
        if (empty($field_of_experience[0]) && empty($experience_industry_name[0]) && empty($experience_designation[0]) && empty($experience_industry_department[0]) && empty($experience_industry_start_date[0]) && empty($experience_industry_end_date[0])) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You Have skipped The Experience Details.']);
            exit;
        }
        if (empty($field_of_experience)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your field of experience.']);
            exit;
        }
        if (empty($experience_industry_name)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your experience industry name.']);
            exit;
        }
        if (empty($experience_designation)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your experience designation.']);
            exit;
        }
        if (empty($experience_industry_department)) {
            echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'Please enter your experience industry department.']);
            exit;
        }


        foreach ($experience_industry_start_date as $key => $value) {
            $experience_industry_start_date[$key] = date(DB_DATE_FORMAT, strtotime($experience_industry_start_date[$key]));
            $experience_industry_end_date[$key] = date(DB_DATE_FORMAT, strtotime($experience_industry_end_date[$key]));
        }
        $procedure_params = [


            ['name' => 'experience_id', 'type' => 'i', 'value' => json_encode($experience_id)],
            ['name' => 'field_of_experience', 'type' => 'i', 'value' => json_encode($field_of_experience)],
            ['name' => 'experience_industry_name', 'type' => 's', 'value' => json_encode($experience_industry_name)],
            ['name' => 'experience_designation', 'type' => 's', 'value' => json_encode($experience_designation)],
            ['name' => 'experience_industry_department', 'type' => 's', 'value' => json_encode($experience_industry_department)],
            ['name' => 'experience_industry_start_date', 'type' => 's', 'value' => json_encode($experience_industry_start_date)],
            ['name' => 'experience_industry_end_date', 'type' => 's', 'value' => json_encode($experience_industry_end_date)],
            ['name' => 'faculty id', 'type' => 'i', 'value' => $logged_user_id],
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],

        ];
        $result = callProcedure("update_pr_faculty_experience_profile_info", $procedure_params);
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
