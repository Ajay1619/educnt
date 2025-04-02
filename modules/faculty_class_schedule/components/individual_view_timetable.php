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

    $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int') : 0;
?>

    <div class="p-6" id="timetable-schedule">
        <div class="subject-assignment">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no-class-timetable.svg' ?>" alt="">
            <p class="action-text">
                No scheduled classes at the moment. Stay tuned for updates!
            </p>
            <div class="action-hint">
                *The best way to predict the future is to create it.*
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await load_individual_view_timetable_schedule();

            } catch (error) {
                // Get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100);
            }
        });
    </script>
<?php

} else {
    echo json_encode(['code' => 400, 'status' => 'success', 'message' => 'Invalid request.']);
    exit;
}
?>