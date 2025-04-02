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
    <div class="main-content-card action-box">
        <h2 class="action-title">Lesson Plan Form</h2>
        <div class="action-box-content">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/Mathematics-amico.svg' ?>" alt="">
            <p class="action-text">
                Select Your Subject To Add The Lesson Plan.
            </p>
            <div class="action-hint">
                *Every lesson plan is a blueprint for shaping future leaders, thinkers, and innovators!*
            </div>
        </div>
        <form method="POST" id="lessonPlanForm" class="lesson-plan-form">
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
                        <span class="text-light" id="lesson-plan-add-academic-year"></span>
                    </div>
                </div>
                <input type="hidden" name="subject_id" class="subject_id" id="subject_id">
                <input type="hidden" name="sem_duriation_id" class="sem_duriation_id" id="sem_duriation_id">
    
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-right">Year Of Study :
                        <span class="text-light" id="lesson-plan-add-year-of-study"></span>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-left">Department :
                        <span class="text-light" id="lesson-plan-add-Department"></span>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="section-header-title text-right">Section :
                        <span class="text-light" id="lesson-plan-add-Section"></span>
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
            <div class="add-sub-form">
                <span class="add-faculty" id="add-topic">Add Topic
                    <button type="button" class="icon tertiary add-faculty-btn">+</button>
                </span>
            </div>
            <button type="submit" class="full-button">Submit</button>
        </form>
            </div>

    <script>
        $(document).ready(async function() {
            $(".lesson-plan-form").hide();
        });
        $(document).ready(function() {
            initializeBulmaCalendar();

            $(".subject-dummy").on("click", function() {
                fetch_Subject($(this));
            });
            $('#lessonPlanForm').on('submit', async function(e) {
                e.preventDefault();
                const data = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_lesson_plan/ajax/faculty_lessonplan_update.php' ?>',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },

                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200 || 300) {
                            showToast(response.status, response.message);
                            const params = {
                                action: 'add',
                                route: 'faculty',
                                type: 'documentupload',
                                tab: 'document'
                            };

                            // Construct the new URL with query parameters
                            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                            const newUrl = window.location.origin + window.location.pathname + queryString;
                            // Use pushState to set the new URL and pass params as the state object
                            window.history.pushState(params, '', newUrl);
                            load_update_admission_profile_components();

                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });
            $("#add-topic").click(function() {
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

            $(document).on("click", ".remove-topic", function() {
                $(this).closest("tr").remove();
            });

            function initializeBulmaCalendar() {
                $(".bulmaCalendar").each(function() {
                    if (!this.bulmaCalendarInstance) {
                        this.bulmaCalendarInstance = bulmaCalendar.attach(this, {
                            type: "date",
                            dateFormat: "<?= BULMA_DATE_FORMAT ?>",
                        })[0];

                        this.bulmaCalendarInstance.on("select", function(datepicker) {
                            this.value = datepicker.data.value();
                        }.bind(this));
                    }
                });
            }

            // $(".selected-lesson-slots-dummy").on("click", function () {
            //     let subid = $("#subject-filter").val();
            //     let date = $(this).closest("tr").find(".dateoflesson").val();

            //     if (!subid || !date) {
            //         alert("Please select a subject and date first.");
            //         return;
            //     }

            //     fetch_slot($(this), subid, date);
            // });
        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>