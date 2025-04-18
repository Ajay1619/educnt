<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
   checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

<div class="stepper">
    <div class="step personalstep">
        <i class="fas fa-user"></i>
        <p>Personal Info</p>
    </div>
    <div class="step educationstep">
        <i class="fas fa-graduation-cap"></i>
        <p>Education Info</p>
    </div>
    <div class="step coursestep">
        <i class="fas fa-chart-line"></i>
        <p>Admission Details </p>
    </div>
    <div class="step feesstep">
        <i class="fas fa-chart-line"></i>
        <p>Fees Details </p>
    </div>
    <div class="step documentuploadstep">
        <i class="fas fa-upload"></i>
        <p>Document Upload</p>
    </div>
</div>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}