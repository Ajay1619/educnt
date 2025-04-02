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
?>
    <div class="main-content-card action-box">
        <div class="attendance-container">
            <h2 class="action-title">Attendance Entry</h2>
            <form id="form_student_attendance_entry_form" method="POST">
                <div class="row">
                    <!-- Subject Dropdown -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="selected-attendance-subjects-dummy" name="selected-subjects" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="selected-attendance-subjects-dummy">Select subject</label>
                            <input type="hidden" name="selected_attendance_subject" class="selected-attendance-subject-filter" id="selected-attendance-subject" required>
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container date">
                            <input type="date" class="bulmaCalendar" id="selected-attendance-date" name="attendance_date" placeholder="dd-MM-yyyy" required>
                            <label class="input-label" for="selected-attendance-date">Select The Date</label>
                        </div>
                    </div>
                    <!-- Slot Selection -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto selected-attendance-slots-dummy dropdown-input" placeholder=" " readonly>
                            <label class="input-label">Select The Slots</label>
                            <input type="hidden" name="selected_attendance_slots" id="selected-attendance-slots" class="selected-attendance-slots" required>
                            <span class="dropdown-arrow">&#8964;</span>

                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                    <!-- Group Selection -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto selected-attendance-group-dummy dropdown-input" placeholder=" " readonly>
                            <label class="input-label">Select The Groups</label>
                            <input type="hidden" name="selected_attendance_group" class="selected-attendance-group">
                            <span class="dropdown-arrow">&#8964;</span>

                            <div class="dropdown-suggestions"></div>
                        </div>
                        <div class="chip-container" id="selected-attendance-group-list-chips"></div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="row mt-6">
                    <div class="col col-12" id="attendance-student-list">
                        <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/student-attendance-action-image.svg' ?>" alt="">
                        <p class="action-text">
                            Select Student's Group To Record The Attendance.
                        </p>
                        <div class="action-hint">
                            *Every student’s success begins with presence. Inspire, engage, and make every moment count!*
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            // Attach Bulma Calendar
            // var calendars = bulmaCalendar.attach('#selected-attendance-date', {
            //     type: 'date',
            //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            //     validateLabel: ""
            // });

            calendars.forEach(calendar => {
                calendar.on('select', function(datepicker) {
                    // Set the selected date value to the input field
                    $('#selected-attendance-slots').val("")
                    $('.selected-attendance-slots-dummy').val("")
                    $('#selected-attendance-group-list-chips').empty()
                    $('#attendance-student-list').html(`
                    <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/student-attendance-action-image.svg' ?>" alt="">
                    <p class="action-text">
                        Select Student's Group To Record The Attendance.
                    </p>
                    <div class="action-hint">
                        *Every student’s success begins with presence. Inspire, engage, and make every moment count!*
                    </div>
                `);
                    document.querySelector('#selected-attendance-date').value = datepicker.data.value();
                });
            });


            $('#selected-attendance-subjects-dummy').on('click', function() {
                const element = $(this);
                fetch_individual_faculty_subject($(this));
            });
            $('#selected-attendance-subject').on('change', function() {
                $('#selected-attendance-date').val("")
                $('#selected-attendance-slots').val("")
                $('.selected-attendance-slots-dummy').val("")
                $('#selected-attendance-group-list-chips').empty()
                $('#attendance-student-list').html(`
                    <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/student-attendance-action-image.svg' ?>" alt="">
                    <p class="action-text">
                        Select Student's Group To Record The Attendance.
                    </p>
                    <div class="action-hint">
                        *Every student’s success begins with presence. Inspire, engage, and make every moment count!*
                    </div>
                `);
            });

            $('.selected-attendance-slots-dummy').on('click', function() {
                if ($('#selected-attendance-subject').val() == "") {
                    showToast("warning", "Please select the Subject first.");
                    return;
                } else if ($('#selected-attendance-date').val() == "") {
                    showToast("warning", "Please select the Date first.");
                    return;
                } else {

                    fetch_faculty_selected_slots_for_day($(this), $('#selected-attendance-date').val(), $('#selected-attendance-subject').val())
                }
            });

            $('.selected-attendance-group-dummy').on('click', function() {
                if ($('#selected-attendance-subject').val() == "") {
                    showToast("warning", "Please select the Subject first.");
                    return;
                } else if ($('#selected-attendance-date').val() == "") {
                    showToast("warning", "Please select the Date first.");
                    return;
                } else if ($('#selected-attendance-slots').val() == "") {
                    showToast("warning", "Please select the Slot first.");
                    return;
                } else {
                    let selectedFacultySubjectId = $('#selected-attendance-subject').val();

                    // Convert the selected value to an integer (if needed)
                    selectedFacultySubjectId = parseInt(selectedFacultySubjectId, 10);

                    // Filter the array
                    let selectedSubject = faculty_subject_list.find(subject => subject.faculty_subjects_id === selectedFacultySubjectId);

                    if (selectedSubject) {
                        sem_duration_id = selectedSubject.sem_duration_id;
                        year_of_study_id = selectedSubject.year_of_study_id;
                        section_id = selectedSubject.section_id;

                        fetch_student_group_list($(this), sem_duration_id, year_of_study_id, section_id)
                    } else {
                        showToast("warning", "No Matched Groups With your Inputs.")
                    }
                }

            });

            $('.selected-attendance-group-dummy').on('blur', async function() {
                setTimeout(async () => {
                    if ($('.selected-attendance-group').val() != "" && $('.selected-attendance-group').val() != null) {
                        createChip($(this), $('#selected-attendance-group-list-chips'), $('.selected-attendance-group').val());
                        $('.chip-close-btn').on('click', async function() {
                            const deselected_group_id = getChipsId($("#selected-attendance-group-list-chips"));
                            showComponentLoading();
                            await fetch_student_name_list(year_of_study_id, section_id, deselected_group_id)
                            hideComponentLoading();

                        });
                        $(".selected-attendance-group").val("");
                        $(this).val("");

                        const selected_group_id = getChipsId($("#selected-attendance-group-list-chips"));
                        showComponentLoading();
                        await fetch_student_name_list(year_of_study_id, section_id, selected_group_id)
                        hideComponentLoading();

                    }

                }, 100);
            });

            //id=form_student_attendance_entry_form on submit using jquery
            $('#form_student_attendance_entry_form').on('submit', async function(e) {
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
                        hideLoading(); // Delay hiding loading by 1 second
                    }, 1000)
                }
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>