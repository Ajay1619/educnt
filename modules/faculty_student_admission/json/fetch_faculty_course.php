<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $procedure_params = [
            
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'user_id', 'value' => $existingAdmissionValue, 'type' => 'i']
        ];
        $data = [
            'student_concession_subject' => 6,  // Example: 6 => "None"
            'student_transport' => 1,  // Example: 1 => "Yes"
            'student_admission_know_about_us' => 2,  // Example: 2 => "Social Media"
            'student_admission_type' => 1,  // Example: 1 => "Centac"
            'lateral_entry_year_of_study' => 2,  // Example: 2 => "Yes" (Lateral Entry)
            'student_hostel' => 1  // Example: 1 => "Hostel"
        ];
        
        // Predefined arrays
        $student_concession = [
            ['title' => "None", 'value' => 6],
            ['title' => "Scholarship", 'value' => 1],
            ['title' => "Government Subsidy", 'value' => 2],
            ['title' => "Sports Quota", 'value' => 3],
            ['title' => "Cultural Quota", 'value' => 4],
            ['title' => "Financial Aid", 'value' => 5]
        ];
        
        $know_about_us = [
            ['title' => "Friends or Family", 'value' => 1],
            ['title' => "Social Media", 'value' => 2],
            ['title' => "Website", 'value' => 3],
            ['title' => "Advertisement", 'value' => 4],
            ['title' => "Events or Workshops", 'value' => 5],
            ['title' => "Other", 'value' => 6]
        ];
        
        $courses = [
            ['title' => "Centac", 'value' => 1],
            ['title' => "Management", 'value' => 2]
        ];
        
        $transport = [
            ['title' => "Yes", 'value' => 1],
            ['title' => "No", 'value' => 0]
        ];
        
        $residency = [
            ['title' => "Hostel", 'value' => 1],
            ['title' => "Days scholar", 'value' => 0]
        ];
        
        $lateral_entry = [
            ['title' => "No", 'value' => 1],
            ['title' => "Yes", 'value' => 2]
        ];
        $result = callProcedure("fetch_pr_admission_course", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if ($result['data']) {
                    $data = $result['data'][0][0];

                    $selected_concession = array_filter($student_concession, function($item) use ($data) {
                        return $item['value'] == $data['student_concession_subject'];
                    });
                    $selected_transport = array_filter($transport, function($item) use ($data) {
                        return $item['value'] == $data['student_transport'];
                    });
                    $selected_know_about_us = array_filter($know_about_us, function($item) use ($data) {
                        return $item['value'] == $data['student_admission_know_about_us'];
                    });
                    $selected_course = array_filter($courses, function($item) use ($data) {
                        return $item['value'] == $data['student_admission_type'];
                    });
                    $selected_residency = array_filter($residency, function($item) use ($data) {
                        return $item['value'] == $data['student_hostel'];
                    });
                    $selected_lateral_entry = array_filter($lateral_entry, function($item) use ($data) {
                        return $item['value'] == $data['lateral_entry_year_of_study'];
                    });
                    $response = [
                        'student_concession' => reset($selected_concession),
                        'student_transport' => reset($selected_transport),
                        'know_about_us' => reset($selected_know_about_us),
                        'student_admission_type' => reset($selected_course),
                        'residency_status' => reset($selected_residency),
                        'lateral_entry_status' => reset($selected_lateral_entry)
                    ];
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data, 'redata' => $response]);
                    exit;
                } else {
                    echo json_encode(['code' => 302, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
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
