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
    $sem_duration_id = isset($_POST['sem_duration_id']) ? sanitizeInput($_POST['sem_duration_id'], 'int') : 0;
    $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
    $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
    $academic_year_id = isset($_POST['academic_year_id']) ? sanitizeInput($_POST['academic_year_id'], 'int') : 0;
    $sem_id = isset($_POST['sem_id']) ? sanitizeInput($_POST['sem_id'], 'int') : 0;
?>
    <!-- Confirm student Allocation Verification Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup" id="hod-student-verification">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Add Student's Group
            </div>

            <form id="student-edit-group-form" method="post">
                <!-- Popup Content -->
                <div class="popup-content">
                    <div class="input-container">
                        <input type="text" id="student_group_input" name="student_group_input" placeholder=" ">
                        <label class="input-label" for="student_group_input">Enter New Group Title</label>

                    </div>
                    <div class="chip-container" id="student-group-chips"></div>
                </div>

                <!-- Popup Footer -->
                <div class="popup-footer">
                    <button type="submit" class="btn-success">Submit</button>
                    <button type="button" class="btn-error deny-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.popup-close-btn, .deny-button').on('click', function() {
                    $('.popup-overlay').remove();
                })
                load_allocated_class_section_groups(<?= $sem_duration_id ?>, <?= $year_of_study_id ?>, <?= $section_id ?>);
                $('#student_group_input').keypress(function(e) {
                    if (e.which == 13 && $(this).val().trim() !== "") {
                        e.preventDefault();
                        createChip($(this), $('#student-group-chips'), 0);
                        $(this).val(""); // Clear the input field
                    }
                });

                $('#student-edit-group-form').submit(async function(e) {
                    e.preventDefault();
                    try {
                        showComponentLoading();
                        const student_group_input_chips_value = getChipsValues($('#student-group-chips'))
                        const student_group_input_chips_id = getChipsId($('#student-group-chips'))
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: 'POST',
                                url: '<?= MODULES . '/faculty_classes/ajax/faculty_classes_student_edit_group_form.php' ?>',
                                headers: {
                                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                                },
                                data: {
                                    'location': window.location.href,
                                    'sem_duration_id': sem_duration_id,
                                    'year_of_study_id': year_of_study_id,
                                    'section_id': section_id,
                                    'academic_year_id': academic_year_id,
                                    'sem_id': sem_id,
                                    'student_group_chips_id': student_group_input_chips_id,
                                    'student_group_chips_value': student_group_input_chips_value
                                },
                                success: function(response) {
                                    response = JSON.parse(response)
                                    showToast(response.status, response.message);
                                    $('#faculty-classes-popup').empty()

                                    resolve();
                                },
                                error: function(jqXHR) {
                                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                                    showToast('error', message);
                                }
                            });
                        });

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