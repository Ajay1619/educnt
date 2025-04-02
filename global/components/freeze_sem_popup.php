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
        <div class="alert-popup" id="freeze-date-popup">
            <div class="popup-header">Semester Management</div>
            <button class="popup-close-btn">×</button>
            <form id="freeze-sem-form" method="post">
                <div class="popup-content" id="freeze-date-popup-content">

                    <div class="input-container date">
                        <input type="date" value="" class="bulmaCalender" id="sem-freeze-date" name="sem_freeze_date" placeholder="dd-MM-yyyy" required aria-required="true">
                        <label class="input-label " for="sem-freeze-date">Select The Date To End The Semester</label>
                    </div>

                </div>
                <div class="popup-footer">
                    <button type="submit" id="submit-btn" class="btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Attach bulmaCalendar to the input
        var calendars = bulmaCalendar.attach('#sem-freeze-date', {
            type: 'date',
            dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            validateLabel: ""
        });

        // Iterate over the array of calendar instances
        calendars.forEach(calendar => {
            calendar.on('select', function(datepicker) {
                // Set the selected date value to the input field
                document.querySelector('#sem-freeze-date').value = datepicker.data.value();
            });
        });


        $('#freeze-sem-form').on('submit', async function(e) {
            e.preventDefault();
            try {
                showComponentLoading()
                const sem_duration_id = <?= $sem_duration_id ?>;
                const year_of_study_id = <?= $year_of_study_id ?>;
                const sem_id = <?= $sem_id ?>;
                await freeze_sem_form_submit(sem_duration_id, year_of_study_id, sem_id);
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