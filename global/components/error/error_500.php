<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
?>

<div class="error-container">
        <div class="error-image">
            <!-- Replace this with your GIF, SVG, or PNG image -->
            <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/download.svg' ?>" alt="Error 400 - Bad Request" />
        </div>
        <div class="error-message">
            <h1>ERROR 500</h1>
            <p>The server encountered an unexpected condition that prevented <br>
                 it from fulfilling the request. Please try again later.

            </p>
            <button onclick="goBack()" class="back-button">
                ← Back
            </button>
        </div>
    </div>
    
    <?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>