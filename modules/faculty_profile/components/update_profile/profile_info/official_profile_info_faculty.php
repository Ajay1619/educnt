<?php
include_once('../../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

?>

    <div class="tab-content active" data-tab-content="1">
        <h2>Official Details</h2>
        <form id="faculty-personal-official-profile-info-faculty-form" method="POST">
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container dropdown-container">
                        <input type="text" id="faculty-designation-dummy" name="faculty_designation_dummy[]" class="auto dropdown-input faculty-designation-dummy" placeholder=" " required readonly>
                        <label class="input-label" for="faculty-designation-dummy">Select Your Designation</label>
                        <input type="hidden" name="faculty_designation" class="faculty-designation" id="faculty-designation">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="faculty-designation-suggestions"></div>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container dropdown-container">
                        <input type="text" id="faculty-dept-dummy" name="faculty_dept_dummy[]" class="auto dropdown-input faculty-dept-dummy" placeholder=" " required readonly>
                        <label class="input-label" for="faculty-dept-dummy">Select Your Department</label>
                        <input type="hidden" name="faculty_dept" class="faculty-dept" id="faculty-dept">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="faculty-dept-suggestions"></div>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="faculty-salary" class="faculty-salary" name="faculty_salary" placeholder=" ">
                        <label class="input-label" for="faculty-salary">Enter Your Salary</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container date">
                        <input type="date" value="" required class="bulmaCalender" id="joining-date" name="joining_date" placeholder="dd-MM-yyyy">
                        <label class="input-label " for="joining-date">Select Your Date of joining</label>
                    </div>
                </div>



            </div>
            <div class="form-navigation">
                <button class="nav-next text-left" id="personal_official_faculty_form_prev_btn" type="button">Previous</button>
                <button class="nav-back text-right" id="personal_official_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(async function() {

            // Initialize the Bulma Calendar
            // var calendars = bulmaCalendar.attach('#joining-date', {
            //     type: 'date',
            //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            //     validateLabel: "",
            // });

            // Set up an event listener to update the input field
            calendars.forEach(calendar => {
                calendar.on('select', function(datepicker) {
                    document.querySelector('#joining-date').value = datepicker.data.value();
                });
            });

            await fetch_faculty_official_details()
            // Previous button click event
            $('#personal_official_faculty_form_prev_btn').on('click', function() {
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'personal',
                    tab: 'address'
                };

                // Construct the new URL with query parameters
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                // Use pushState to set the new URL and pass params as the state object
                window.history.pushState(params, '', newUrl);
                load_personal_info_components();
            });

            $('.faculty-designation-dummy').on('click focus', function() {
                fetch_faculty_designation($(this));
            });
            $('.faculty-dept-dummy').on('click focus', function() {
                fetch_dept_list($(this))
            });

            $('#faculty-personal-official-profile-info-faculty-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_personal_official_profile_info_faculty_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: formData,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            const params = {
                                action: 'add',
                                route: 'faculty',
                                type: 'education',
                                tab: 'schoolings'
                            };


                            // Construct the new URL with query parameters
                            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                            const newUrl = window.location.origin + window.location.pathname + queryString;
                            // Use pushState to set the new URL and pass params as the state object
                            window.history.pushState(params, '', newUrl);
                            load_update_profile_components();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });

        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
