<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

    <form method="GET" id="EditLessonPlanForm" class="edit-lesson-plan-form">
        <!-- <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" class="auto subject-dummy dropdown-input" readonly placeholder=" " value="">
                        <label class="input-label">Select The Subject</label>
                        <input type="hidden" name="subject_filter" id="subject-filter" class="subject-filter">
                        <input type="hidden" name="year_filter" id="year-filter" class="year-filter">
    
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions"></div>
                    </div>
                </div>
            </div> -->
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="section-header-title text-left">Academic Year :
                    <span class="text-light" id="lesson-plan-edit-academic-year"></span>
                </div>
            </div>
            <input type="hidden" name="subject_id" class="subject_id" id="subject_id">
            <input type="hidden" name="sem_duriation_id" class="sem_duriation_id" id="sem_duriation_id">

            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="section-header-title text-right">Year Of Study :
                    <span class="text-light" id="lesson-plan-edit-year-of-study"></span>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="section-header-title text-left">Department :
                    <span class="text-light" id="lesson-plan-edit-Department"></span>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="section-header-title text-right">Section :
                    <span class="text-light" id="lesson-plan-edit-Section"></span>
                </div>
            </div>
        </div>
        <h4>Lesson Plan Topics </h4>
        <table id="topics-table" class="portal-table ">
            <thead>
                <tr>
                    <th>Topic</th>
                    <th>Date</th>
                    <th>Timing Slot</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <div id="lesson-plan-table"></div>
            </tbody>
        </table>
        <div class="edit-sub-form">
            <span class="edit-faculty" id="edit-topic">Edit Topic
                <button type="button" class="icon tertiary edit-faculty-btn">+</button>
            </span>
        </div>
        <button type="submit" class="full-button">Submit</button>
    </form>
<script>
    $("#edit-topic").click(function() {
                var newRow = `<tr>
                    <td>
                        <div class="input-container">
                            <input type="text" name="lesson_plan_topic_text[]" placeholder="Enter Topic" required>
                        </div>
                    </td>
                    <td>
                        <div class="input-container date">
                            <input type="date" class="bulmaCalendar" id="dateoflesson" name="topic_date[]" placeholder="dd-MM-yyyy" required>
                            <label class="input-label" for="dateoflesson">Select The Date</label>
                        </div>
                    </td>
                    <td>
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto selected-lesson-slots-dummy dropdown-input" readonly placeholder="Select The Slots">
                            <input type="hidden" name="selected_lesson_slots[]" class="selected-lesson-slots">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="remove-topic">X</button>
                    </td>
                </tr>`;

                $("#topics-table tbody").append(newRow);
                //initializeBulmaCalendar();
                $(".selected-lesson-slots-dummy").on("click", function() {
                    let subid = $("#subject-filter").val();
                    let date = $(this).closest("tr").find(".dateoflesson").val();

                    if (!subid || !date) {
                        alert("Please select a subject and date first.");
                        return;
                    }

                    fetch_slot($(this), subid, date);
                });
            });
</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>