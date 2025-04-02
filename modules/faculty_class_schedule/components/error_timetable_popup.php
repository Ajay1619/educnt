<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $timetable_data = isset($_POST['timetable_data']) ? sanitizeInput($_POST['timetable_data'], 'string') : '';


?>

    <!-- warning Popup Overlay -->
    <div class="warning-popup-overlay">
        <!-- warning Popup Container -->
        <div class="warning-popup">
            <!-- Close Button -->
            <button class="warning-close-btn">×</button>

            <!-- Popup Header -->
            <div class="warning-header">
                <h2 class="warning-title">warning Occurred</h2>
            </div>

            <!-- Popup Content -->
            <div class="warning-content">
                <div class="row">
                    <div class="col col-8">
                        <!-- warning Message -->
                        <h5 class="warning-popup-header">
                            Something went wrong! ⚠️
                        </h5>
                        <!-- Motivational Quote -->
                        <p class="warning-quotes"></p>
                    </div>

                    <div class="col col-4">
                        <!-- warning Image -->
                        <div class="warning-image-container">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/warning_popup.gif' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        
        $('.warning-close-btn').click(function() {
            // Add slide-up and fade-out classes
            $('.warning-popup').addClass('slide-up');
            $('.warning-popup-overlay').addClass('fade-out');
            $('#error_timetable').empty();
        });
    </script>

<?php

} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>