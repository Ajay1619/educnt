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
?>

    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-button" id="faculty-lesson-plan-add-button">Add Lesson Plan</button>
                        <button class="outline bg-card-button" id="faculty-lesson-plan-view-button">View Lesson Plan</button>
                        <button class="outline bg-card-back-button" id="bg-card-back-button">Back</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="selected-lesson-plan-subjects-dummy-filter" name="selected-subjects" class=" dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="selected-attendance-subjects-dummy">Select subject</label>
                            <input type="hidden" name="selected_attendance_subject_filter" class="selected-lesson-plan-subject-filter" id="selected-lesson-plan-subject-filter" required>
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            try {
                $('#faculty-lesson-plan-add-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'add',
                        });
                        await callAction();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });
                $('#faculty-lesson-plan-view-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                        });
                        await callAction();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });
                $('#selected-lesson-plan-subjects-dummy-filter').on('click', function() {
                    const element = $(this);
                    fetch_individual_faculty_subject($(this));
                });

                $('#selected-lesson-plan-subjects-dummy-filter').on('blur', function() {
                    setTimeout(async () => {
                        if ($('#selected-lesson-plan-subject-filter').val() != '' && $('#selected-lesson-plan-subject-filter').val() != null) {
                            showComponentLoading()
                            await load_subjectwise_attendance_table($('#selected-lesson-plan-subject-filter').val());
                            setTimeout(function() {
                                hideComponentLoading(); // Delay hiding loading by 1 second
                            }, 100)
                        }
                    }, 100);
                });
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
        callAction($("#action"));
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>