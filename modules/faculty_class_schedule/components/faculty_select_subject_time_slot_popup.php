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
    $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
    $subject_id = isset($_POST['subject_id']) ? sanitizeInput($_POST['subject_id'], 'int') : 0;
    $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
    $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
    $sem_duration_id = isset($_POST['sem_duration_id']) ? sanitizeInput($_POST['sem_duration_id'], 'int') : 0;
?>
    <!-- Confirmation Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup half-width">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Select Slot
            </div>

            <!-- Popup Content -->
            <div class="popup-content">
                <form id="faculty_select_subject_time_slot_form" method="post">
                    <div class="input-container dropdown-container">
                        <input type="text" class="auto selected-day-dummy dropdown-input" placeholder=" " value="">
                        <label class="input-label">Select The Day</label>
                        <input type="hidden" name="selected_day" id="selected-day" class="selected-day">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions"></div>
                    </div>
                    <div class="input-container autocomplete-container">
                        <input type="text" class="auto selected-slots-dummy autocomplete-input" placeholder=" " value="">
                        <label class="input-label">Select The slots</label>
                        <input type="hidden" name="selected_slots" class="selected-slots">
                        <span class="autocomplete-arrow">&#8964;</span>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="chip-container" id="selected-slots-list-chips"></div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <div class="popup-action-buttons">
                    <button class="btn-success slot-selection-rest-confirm-btn">Save</button>
                    <button class="btn-error slot-selection-rest-cancel-btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.slot-selection-rest-cancel-btn , .popup-close-btn').on('click', function() {
                    $("#class-schedules-popup").empty();
                    $('.individual-subject-list').removeClass('active');
                });

                $('.selected-day-dummy').on('click focus', function() {
                    const element = $(this);
                    fetch_day_list(element, 1);
                });

                $('.selected-day-dummy').on('blur', function() {
                    setTimeout(() => {
                        if ($("#selected-day").val() != null || $("#selected-day").val() != "") {

                            $('#selected-slots-list-chips').empty();
                            fetch_faculty_selected_slots_for_day($("#selected-day").val(), <?= $subject_id ?>)
                        }
                    }, 100);
                });

                $('.selected-slots-dummy').on('click', function() {
                    if ($("#selected-day").val() == null || $("#selected-day").val() == "") {
                        showToast("warning", "Please select the day first.");
                        return;
                    }
                    const element = $(this);
                    const day_id = $("#selected-day").val();
                    fetch_dept_slots_list(element, <?= $dept_id ?>, 1, day_id, <?= $year_of_study_id ?>, <?= $section_id ?>, <?= $sem_duration_id ?>);
                });

                $('.selected-slots-dummy').on('blur', function() {
                    setTimeout(() => {
                        console.log($('.selected-slots').val())
                        if ($('.selected-slots').val() != 0) {
                            createChip($(this), $('#selected-slots-list-chips'), $('.selected-slots').val());

                            $(this).val(""); // Clear the input field
                            $('.selected-slots').val("0"); // Clear the input field
                            selected_slots_list_chips = getChipsValues($('#selected-slots-list-chips'));
                        }

                    }, 200);
                });

                $('.slot-selection-rest-confirm-btn').on('click', async function() {
                    showComponentLoading(2);
                    const selected_day = $("#selected-day").val();
                    const subject_id = <?= $subject_id ?>;
                    const sem_duration_id = <?= $sem_duration_id ?>;
                    const selected_slots_list = getChipsId($('#selected-slots-list-chips'));
                    const params = {
                        selected_day,
                        selected_slots_list,
                        subject_id,
                        sem_duration_id,
                    };
                    await add_subject_selected_slots(params);
                    $("#class-schedules-popup").empty();
                    $('.individual-subject-list').removeClass('active');
                    setTimeout(async function() {

                        await load_individual_allocated_subject_list();
                        await load_individual_allocated_subject_slot_list();
                        hideComponentLoading();
                    }, 100);
                });

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