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

        $skills = isset($_POST['skills']) ? sanitizeInput($_POST['skills'], 'string') : [];
        $software_skills = isset($_POST['software_skills']) ? sanitizeInput($_POST['software_skills'], 'string') : [];
        $interest = isset($_POST['interest']) ? sanitizeInput($_POST['interest'], 'string') : [];
        $languages = isset($_POST['languages']) ? sanitizeInput($_POST['languages'], 'string') : [];

        if (empty($skills) && empty($software_skills) && empty($software_skills) && empty($languages)) {
            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'You Have Skipped The Skills Details.']);
            exit;
        }
        $procedure_params = [

            ['name' => 'faculty id', 'type' => 'i', 'value' => $logged_user_id],
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'skills', 'type' => 's', 'value' => json_encode($skills)],
            ['name' => 'software_skills', 'type' => 's', 'value' => json_encode($software_skills)],
            ['name' => 'interest', 'type' => 's', 'value' => json_encode($interest)],
            ['name' => 'languages', 'type' => 's', 'value' => json_encode($languages)]

        ];
        $result = callProcedure("update_pr_faculty_skill_profile_info", $procedure_params);
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
