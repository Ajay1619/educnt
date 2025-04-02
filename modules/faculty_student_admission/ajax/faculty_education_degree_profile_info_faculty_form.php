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

        $degree_id = isset($_POST['degree_id']) ? sanitizeInput($_POST['degree_id'], 'int') : '';
        $degree_institution_name = isset($_POST['degree_institution_name']) ? sanitizeInput($_POST['degree_institution_name'], 'string') : '';
        $education_degree = isset($_POST['education_degree']) ? sanitizeInput($_POST['education_degree'], 'int') : 0;
        $education_degree_specialization = isset($_POST['education_degree_specialization']) ? sanitizeInput($_POST['education_degree_specialization'], 'int') : '';
        $degree_passed_out_year = isset($_POST['degree_passed_out_year']) ? sanitizeInput($_POST['degree_passed_out_year'], 'string') : '';
        $degree_percentage = isset($_POST['degree_percentage']) ? sanitizeInput($_POST['degree_percentage'], 'string') : '';
        $degree_cgpa = isset($_POST['degree_cgpa']) ? sanitizeInput($_POST['degree_cgpa'], 'string') : '';
     
        if ($degree_percentage[0] == "" && $degree_institution_name[0] == ""  ) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You Skiped Degree Education Information!']);
            exit;
        } 
        $procedure_params = [

            ['name' => 'faculty id', 'type' => 'i', 'value' => $existingAdmissionValue],
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'degree instittuion name', 'type' => 's', 'value' => json_encode($degree_institution_name)],
            ['name' => 'education_degree', 'type' => 's', 'value' => json_encode($education_degree)],
            ['name' => 'education_degree_specialization', 'type' => 's', 'value' => json_encode($education_degree_specialization)],
            ['name' => 'degree_passed_out_year', 'type' => 's', 'value' => json_encode($degree_passed_out_year)],
            ['name' => 'degree_percentage', 'type' => 's', 'value' => json_encode($degree_percentage)],
            ['name' => 'degree_cgpa', 'type' => 's', 'value' => json_encode($degree_cgpa)],
            ['name' => 'degree_id', 'type' => 's', 'value' => json_encode($degree_id)]

        ];
         
        $result = callProcedure("update_stu_admission_faculty_education_degree_profile_info", $procedure_params);

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
