
<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
   // isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    // if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    //     echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
    //     exit;
    // }
?>

<h1>Dashboard</h1>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}