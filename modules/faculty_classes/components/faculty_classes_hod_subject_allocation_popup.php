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
    $verifying_year_of_study_title = isset($_POST['verifying_year_of_study_title']) ? sanitizeInput($_POST['verifying_year_of_study_title'], 'string') : '';
    $verifying_section_title = isset($_POST['verifying_section_title']) ? sanitizeInput($_POST['verifying_section_title'], 'string') : '';
    $verifying_year_of_study_id = isset($_POST['verifying_year_of_study_id']) ? sanitizeInput($_POST['verifying_year_of_study_id'], 'int') : '';
    $verifying_section_id = isset($_POST['verifying_section_id']) ? sanitizeInput($_POST['verifying_section_id'], 'int') : '';
?>
    <!-- Confirm Subject Allocation Verification Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup" id="hod-subject-verification">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Confirm Subject Allocation Verification
            </div>

            <form id="subject-allocation-verification-form" method="post">
                <!-- Popup Content -->
                <div class="popup-content">
                    <p class="popup-quotes">
                        Are you sure you want to verify the subject allocation for
                        <b> <strong><?= $verifying_year_of_study_title ?></strong> - Year ,
                            <strong><?= $verifying_section_title ?></strong> - Section?</b>
                        <br><br>
                        <span class="error-text"> <strong>Note:</strong> This action cannot be reversed once confirmed.</span>
                    </p>
                </div>

                <!-- Popup Footer -->
                <div class="popup-footer">
                    <button type="submit" class="btn-success">Yes, Verify</button>
                    <button type="button" class="btn-error deny-button">No, Deny</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                //#subject-allocation-verification-form on submit
                $('#subject-allocation-verification-form').submit(async function(e) {
                    e.preventDefault();
                    verify_pending_hod_subject_allocation(<?= $verifying_year_of_study_id ?>, <?= $verifying_section_id ?>)
                })

                //class=popup-close-btn on click
                $('.popup-close-btn, .deny-button').on('click', function() {
                    $('.popup-overlay').remove();
                })
            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100)
            }
        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>