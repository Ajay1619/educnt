<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $dept_id = !in_array($logged_role_id, $tertiary_roles) ? $logged_dept_id : 0;
    $faculty_id = in_array($logged_role_id, $tertiary_roles) ? $logged_user_id : 0;

?>
    <div class="main-content-card">
        <div class="action-box class-schedule-subject-allocation-class-section">
            <div class="action-title">Time Table</div>
            <div id="schedule"></div>
            <div id="individual-timetable-summary" class="p-6"></div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                if (<?= $faculty_id ?> != 0) {
                    await load_individual_view_timetable(<?= $faculty_id ?>);
                } else {
                    await load_dept_view_timetable($(".dept-filter").val(), $(".year-of-study-filter").val(), $(".section-filter").val());
                }
                // Optional functionality
                /* $('#subject-allocation-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'edit',
                        type: 'subject_allocation'
                    });
                    await load_faculty_classes_components();
                }); */

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