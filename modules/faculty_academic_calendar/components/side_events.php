<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
    <div class="col col-6 col-lg-3 col-md-3 col-sm-12 col-xs-12">
    <div class="side_events">
        <h3>Events</h3>
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    Work
                    <span class="accordion-icon">+</span>
                </div>
                <div class="accordion-content" style="display: none;">
                    <div class="event" draggable="true">Meeting</div>
                    <div class="event" draggable="true">Project Deadline</div>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header">
                    Education
                    <span class="accordion-icon">+</span>
                </div>
                <div class="accordion-content" style="display: none;">
                    <div class="event" draggable="true">Exam</div>
                    <div class="event" draggable="true">Functions</div>
                    <div class="event" draggable="true">College Orientation</div>
                    <div class="event" draggable="true">Graduation Ceremony</div>
                    <div class="event" draggable="true">Sports Day</div>
                    <div class="event" draggable="true">Semester Break</div>
                    <div class="event" draggable="true">Annual Fest</div>
                </div>
            </div>
        </div>
    </div>
</div>


    <script src="<?= MODULES . '/faculty_academic_calendar/js/events_accordion.js' ?>"></script>
    <script>
        $(document).ready(async function() {
            try {
                await accordion_events_calendar();


            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>