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

    <!-- success Popup Overlay -->
    <div class="success-popup-overlay">
        <!-- success Popup Container -->
        <div class="success-popup">
            <!-- Close Button -->
            <button class="success-close-btn">×</button>

            <!-- Popup Header -->
            <div class="success-header">
                <h2 class="success-title">Success </h2>
            </div>

            <!-- Popup Content -->
            <div class="success-content">
                <div class="row">
                    <div class="col col-8">
                        <!-- success Message -->
                        <h5 class="success-popup-header">
                            Enabled the slot 👍
                        </h5>
                        <!-- Motivational Quote -->
                        <p class="success-quotes">
                        The slot has been enabled and is now available.
                        </p>
                    </div>

                    <div class="col col-4">
                        <!-- success Image -->
                        <div class="success-image-container">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/breaking.gif' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        
        $('.success-close-btn').click(function() {
            // Add slide-up and fade-out classes
            $('.success-popup').addClass('slide-up');
            $('.success-popup-overlay').addClass('fade-out');
            $('#success_timetable').empty();

        });
    </script>

<?php

} else {
    echo json_encode(['code' => 400, 'status' => 'success', 'message' => 'Invalid request.']);
    exit;
}
?>