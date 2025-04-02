 
<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

    
    $existing_id = isset($_POST['existing_id']) ? sanitizeInput($_POST['existing_id'], 'string') : null;

    if (!$existing_id) {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Existing ID is required.']);
        exit;
    }

    try {
        // Prepare parameters for your procedure call (you may want to adjust this according to your logic)
        $procedure_params = [
            ['name' => 'existing_id', 'type' => 's', 'value' => $existing_id], // assuming existing_id is a string
        ];

        $result = callProcedure('fetch_student_admission_personal_info', $procedure_params); // Adjust the procedure name as needed

        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                $data = $result['data'][0];
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message'],
                    'data' => $data
                ]);
            } else {
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message']
                ]);
            }
        } else {
            echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Failed to retrieve response.']);
        }
    } catch (\Throwable $th) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $th->getMessage()]);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
}
