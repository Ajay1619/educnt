<?php
include_once('../../config/sparrow.php');

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
    $sem_id = isset($_POST['sem_id']) ? sanitizeInput($_POST['sem_id'], 'int') : 0;
?>
    <div class="popup-overlay" id="active-popup-date">
        <div class="alert-popup" id="begin-date-popup">
            <div class="popup-header">Semester Management</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content" id="begin-date-popup-content">
                <form id="begin-sem-form" method="post">
                    <div class="input-container date">
                        <input type="text" id="sem-title" value="Odd Sem" readonly>
                        <label class="input-label " for="sem-title">Semester Title</label>
                    </div>
                    <div class="input-container date mt-6">
                        <input type="date" value="" class="bulmaCalender" id="sem-begin-date" name="sem_begin_date" placeholder="dd-MM-yyyy" required aria-required="true">
                        <label class="input-label " for="sem-begin-date">Select The Date To Begin The Semester</label>
                    </div>
                    <div class="popup-footer">
                        <button type="submit" id="submit-btn" class="particulars btn-success">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script>
        var calendars = bulmaCalendar.attach('#sem-begin-date', {
            type: 'date',
            dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            validateLabel: ""
        });

        calendars.forEach(calendar => {
            calendar.on('select', function(datepicker) {
                // Set the selected date value to the input field
                document.querySelector('#sem-begin-date').value = datepicker.data.value();
            });
        });

        $('#begin-sem-form').on('submit', async function(e) {
            e.preventDefault();
            try {
                showComponentLoading()
                const sem_duration_id = <?= $sem_duration_id ?>;
                const year_of_study_id = <?= $year_of_study_id ?>;
                const sem_id = <?= $sem_id ?>;
                await begin_sem_form_submit(sem_duration_id, year_of_study_id, sem_id);
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