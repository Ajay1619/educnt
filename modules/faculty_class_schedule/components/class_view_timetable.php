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

    $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
    $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
    $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
?>


    <div class="p-6" id="timetable-schedule">

        <div class="subject-assignment">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no-class-timetable.svg' ?>" alt="">
            <p class="action-text">
                It looks like no classes are assigned for this department yet. <br>
                Please refine your search by selecting a Year of Study and Section.
            </p>
            <div class="action-hint">
                *Knowledge grows when you seek it—give it another try!*
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await load_class_view_timetable_schedule(<?= $dept_id ?>, <?= $year_of_study_id ?>, <?= $section_id ?>);

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