<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="main-content-card">
        <div class="action-box class-schedule-subject-allocation-class-section">
            <div class="action-title">Subject Schedule Assignment Panel</div>
            <div class="row">
                <!-- Subject List -->
                <div class="col col-4 col-lg-3 col-md-3 col-smy-3 col-xs-3">
                    <div class="main-content-card" id="subject-list"></div>
                </div>
                <!-- Timetable -->
                <div class="col col-4 col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    <div class="main-content-card p-6" id="timetable-schedule">
                        <div class="subject-assignment">
                            <p class="action-text">
                                Select a Subject and Schedule Your Time Slot.
                            </p>
                            <div class="action-hint">
                                *Your destiny is in your hands. Schedule wisely, for the future awaits!*
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {


            try {
                showComponentLoading();
                await load_individual_allocated_subject_list();
                await load_individual_allocated_subject_slot_list();



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
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>