<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'  // Change to POST
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
    
    header('Content-Type: application/json'); // Set content type for JSON

    // Correctly structured PHP array
    $response = [
        "roles" => [
            [
                "name" => "Class Advisor",
                "designation" => "Senior Teacher",
                "role_type" => "primary",
                "description" => "Responsible for guiding students and coordinating with faculty.",
                "academic_year" => "2024-2025"
            ],
            [
                "name" => "Sports Committee Member",
                "designation" => "Physical Education Teacher",
                "role_type" => "committee",
                "description" => "Organizes and oversees sports events.",
                "academic_year" => "2024-2025"
            ],
            [
                "name" => "Science Club Member",
                "designation" => "Science Teacher",
                "role_type" => "additional",
                "description" => "Encourages students to explore science through experiments.",
                "academic_year" => "2024-2025"
            ],
            [
                "name" => "Math Tutor",
                "designation" => "Junior Teacher",
                "role_type" => "primary",
                "description" => "Provides tutoring support for students struggling in math.",
                "academic_year" => "2024-2025"
            ],
            [
                "name" => "Cultural Committee Chair",
                "designation" => "Arts Teacher",
                "role_type" => "committee",
                "description" => "Leads cultural activities and events in the school.",
                "academic_year" => "2024-2025"
            ]
        ]
    ];

    // Output the JSON response
    echo json_encode($response);
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
