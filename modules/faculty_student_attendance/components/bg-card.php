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
    // print_r($faculty_page_access_data);
?>
    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-add-button" id="faculty-student-attendance-add-button">Add</button>
                        <button class="outline bg-card-view-button" id="faculty-student-attendance-view-button">View</button>
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
                    <?php if (in_array($logged_role_id, $primary_roles)) { ?>
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="att-dept-filter">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto att-dept-filter-dummy dropdown-input" placeholder=" " value="">
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="att_dept_filter" class="att-dept-filter">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="selected-attendance-subjects-dummy-filter" name="selected-subjects" class=" dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="selected-attendance-subjects-dummy">Select subject</label>
                            <input type="hidden" name="selected_attendance_subject_filter" class="selected-attendance-subject-filter" id="selected-attendance-subject-filter" required>
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
                $('#faculty-student-attendance-add-button').on('click', async function() {

                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'add',
                            route: 'faculty',
                        });
                        await load_main_components();
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

                $('#faculty-student-attendance-view-button').on('click', async function() {

                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'view',
                            route: 'faculty',
                        });
                        await load_main_components();
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

                $('.att-dept-filter-dummy').on('click', async function() {
                    fetch_dept_list($(this));
                });
                $('#selected-attendance-subjects-dummy-filter').on('click', async function() {
                    const element = $(this);
                    if (<?= json_encode($primary_roles) ?>.includes(<?= $logged_role_id ?>)) {
                        if ($('.att-dept-filter').val() != '' && $('.att-dept-filter').val() != null) {
                            await fetch_individual_faculty_subject(element, $('.att-dept-filter').val());
                        } else {
                            showToast('warning', "Please select the department first.");
                        }
                    } else {

                        await fetch_individual_faculty_subject($(this), 0);
                    }

                });

                $('#selected-attendance-subjects-dummy-filter').on('blur', function() {
                    setTimeout(async () => {
                        if ($('#selected-attendance-subject-filter').val() != '' && $('#selected-attendance-subject-filter').val() != null) {
                            showComponentLoading()
                            await load_subjectwise_attendance_table($('#selected-attendance-subject-filter').val());
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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>