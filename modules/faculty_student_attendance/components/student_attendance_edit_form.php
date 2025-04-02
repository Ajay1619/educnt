<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
    $faculty_subjects_id = $_POST['faculty_subjects_id'];
    $attendance_date = $_POST['attendance_date'];
    $selected_attendance_slot = $_POST['selected_attendance_slot'];
?>
    <div class="main-content-card action-box">
        <div class="attendance-container">
            <h2 class="action-title">Attendance Edit</h2>
            <form id="form_student_attendance_edit_form" method="POST">
            </form>
        </div>
    </div>
    <!-- JavaScript -->
    <script>
        $(document).ready(async function() {
            try {

                showComponentLoading();
                $('.full-width-hr').hide();
                $('.bg-card-filter').hide();
                load_individual_student_subjectwise_attendance_edit_table('<?= $faculty_subjects_id ?>', '<?= $attendance_date ?>', '<?= $selected_attendance_slot ?>')
                //id=form_student_attendance_entry_form on submit using jquery
                $('#form_student_attendance_edit_form').on('submit', async function(e) {
                    e.preventDefault();
                    try {
                        const data = $(this).serialize();
                        await submit_form_student_attendance_entry_form(data)
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
                        }, 1000)
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
                }, 1000)
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>