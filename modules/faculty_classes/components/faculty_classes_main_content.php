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
?>
    <div class="tabs mt-6">
        <div class="tab" id="subject-allocation-tab">Subject Allocation</div>
        <div class="tab" id="student-allocation-tab">Student Allocation</div>
    </div>

    <div class="main-content-card action-box">
        <div class="action-title"></div>
        <?php if ($logged_role_id != 6) { ?>
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-left">Academic Year :
                        <span class="text-light" id="class-manager-academic-year"></span>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-right">Year Of Study :
                        <span class="text-light" id="class-manager-year-of-study"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-left">Semester :
                        <span class="text-light" id="class-manager-semester"></span>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-right">Section :
                        <span class="text-light" id="class-manager-section"></span>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div id="allocation-content"></div>
    </div>
    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                <?php if ($logged_role_id != 6) { ?>
                    await fetch_faculty_classes_class_advisors_details()
                    await load_faculty_classes_components()
                    await tabs_active()

                    $('#subject-allocation-tab').click(async function() {
                        updateUrl({
                            route: 'faculty',
                            action: 'edit',
                            type: 'subject_allocation'
                        })
                        await load_faculty_classes_components()
                    });
                    $('#student-allocation-tab').click(async function() {
                        await updateUrl({
                            route: 'faculty',
                            action: 'edit',
                            type: 'student_allocation'
                        })
                        await load_faculty_classes_components()
                    });

                <?php } else { ?>
                    await load_faculty_classes_components()
                    await tabs_active()

                    $('#subject-allocation-tab').click(async function() {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                            type: 'subject_allocation'
                        })
                        await load_faculty_classes_components()
                    });
                    $('#student-allocation-tab').click(async function() {
                        await updateUrl({
                            route: 'faculty',
                            action: 'view',
                            type: 'student_allocation'
                        })
                        await load_faculty_classes_components()
                    });
                <?php } ?>
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