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
    <form method="POST" id="faculty_student_admission_course_details_form">
        <div class="tab-content active" data-tab-content="1">
            <div class='section-header-title text-left m-6'>Course Preferences</div>
            <div class="row">
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="1st-course-dummy" class="dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="1st-course-dummy">Select 1st Course Preference</label>
                        <input type="hidden" name="1st_course_preference" id="1st-course">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="1st-course-suggestions"></div>
                    </div>
                </div>

                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="2nd-course-dummy" class="dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="2nd-course-dummy">Select 2nd Course Preference</label>
                        <input type="hidden" name="2nd_course_preference" id="2nd-course">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="2nd-course-suggestions"></div>
                    </div>
                </div>

                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="3rd-course-dummy" class="dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="3rd-course-dummy">Select 3rd Course Preference</label>
                        <input type="hidden" name="3rd_course_preference" id="3rd-course">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="3rd-course-suggestions"></div>
                    </div>
                </div>
            </div>

            <div class='section-header-title text-left m-6'>General Details</div>
            <div class="row">
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-residency-dummy-1" class="student-residency-dummy auto dropdown-input" name="student-residency-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student-residency-dummy-1">Select Student Residency Status</label>
                        <input type="hidden" name="student_residency_status" class="student-residency" id="student-residency-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student-residency-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-reference-dummy-1" class="student-reference-dummy auto autocomplete-input" name="student-reference-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student-reference-dummy-1">Who referred this student?</label>
                        <input type="hidden" name="student_reference_name" class="student-reference" id="student-reference-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student-reference-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-transport-dummy-1" class="student-transport-dummy auto autocomplete-input" name="student-transport-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student-transport-dummy-1">Select Student transport</label>
                        <input type="hidden" name="student_transport_name" class="student-transport" id="student-transport-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student-transport-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-know-about-us-dummy-1" class="student-know-about-us-dummy auto autocomplete-input" name="student-know-about-us-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student-know-about-us-dummy-1">How Do You Know About Us</label>
                        <input type="hidden" name="student_know_about_us" class="student-know-about-us" id="student-know-about-us-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student-know-about-us-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-concession" class="student_concession-dummy auto autocomplete-input" name="student_concession-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student_concession">Student Concession</label>
                        <input type="hidden" name="student_concession" class="student_concession" id="student-concession-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student_concession-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="student-type-of-admission-dummy-1" class="student-type-of-admission-dummy auto dropdown-input" name="student-type-of-admission-dummy[]" placeholder=" " readonly required>
                        <label class="input-label" for="student-type-of-admission-dummy-1">Select Student Type of Admission</label>
                        <input type="hidden" name="type_of_admission_status" class="student-type-of-admission" id="student-type-of-admission-1">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="student-type-of-admission-suggestions"></div>
                    </div>
                </div>
            </div>

            <div class='section-header-title text-left m-6'>Additional Details</div>
            <div class="row">
                <!-- Lateral Entry Option -->
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">

                    <div class="input-container dropdown-container">
                        <input type="text" id="lateral-entry-dummy" class="dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="lateral-entry-dummy">Are you a Lateral Entry?</label>
                        <input type="hidden" name="lateral_entry" id="lateral-entry">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="lateral-entry-suggestions"></div>
                    </div>
                </div>

            </div>

            <div id="lateral-entry-details" style="display: none;">
                <div class="row">
                    <!-- Register Number -->
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="register-number" name="register_number" class="dropdown-input" placeholder=" ">
                            <label class="input-label" for="register-number">Enter Register Number</label>
                        </div>
                    </div>

                    <!-- Continuing Year -->
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="continuing-year" name="continuing_year" class="dropdown-input" placeholder=" ">
                            <label class="input-label" for="continuing-year">Enter Continuing Year</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-navigation ">
                <button class="btn prev-btn text-left" id="course_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="btn next-btn text-right" id="course_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>

    </form>

    <script>
        $(document).ready(async function() {
            try {
                await fetch_faculty_course();
                // Toggle display of additional fields based on Lateral Entry selection
                $('#lateral-entry-dummy').on('blur focus', async function() {
                    setTimeout(() => {
                        // console.log($(this).val());

                        if ($(this).val() == 'Yes') {
                            $('#lateral-entry-details').show();
                            $('#register-number, #continuing-year').prop('required', true);
                        } else {
                            $('#lateral-entry-details').hide();
                            $('#register-number, #continuing-year').prop('required', false).val('');
                        }
                    }, 500); // 1-second delay (1000 milliseconds)
                });



                // Existing script for handling dropdowns
                $('#1st-course-dummy,#2nd-course-dummy,#3rd-course-dummy').on('click focus', async function() {
                    console.log('he');
                    await fetch_department_degrees($(this));
                });

                $('#student-reference-dummy-1').on('click focus', async function() {
                    console.log('reference');
                    await fetch_faculty_list_degrees($(this));
                });

                $('#lateral-entry-dummy').on('click focus', async function() {
                    console.log('reference');
                    await fetch_faculty_lateral_entry($(this));
                });

                $('#student-residency-dummy-1').on('click focus', async function() {
                    console.log('residency');
                    await fetch_faculty_residency($(this));
                });

                $('#student-transport-dummy-1').on('click focus', async function() {
                    console.log('transport');
                    await fetch_faculty_transport($(this));
                });

                $('#student-type-of-admission-dummy-1').on('click focus', async function() {
                    console.log('admission');
                    await fetch_faculty_admission_type($(this));
                });

                $('#student-know-about-us-dummy-1').on('click focus', async function() {
                    console.log('admission');
                    await fetch_faculty_know_about_us($(this));
                });

                $('.student_concession-dummy').on('click focus', async function() {
                    console.log('student_concession');
                    await fetch_faculty_concession($(this));
                });
                $('#course_info_faculty_form_prev_btn').on('click', async function() {
                    showComponentLoading(1)

                    const params = {
                        action: 'add',
                        route: 'faculty',
                        type: 'education',
                        tab: 'degree'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    loadUrlBasedOnURL();

                    //load_personal_info_components();
                    await loadUrlBasedOnURL()
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
                });
                $('#faculty_student_admission_course_details_form').on('submit', async function(e) {
                    showComponentLoading(2)

                    e.preventDefault();
                    const data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: '<?= MODULES . '/faculty_student_admission/ajax/faculty_course.php' ?>',
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
                                    type: 'fees',
                                    tab: 'fees_details'
                                };

                                // Construct the new URL with query parameters
                                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                                const newUrl = window.location.origin + window.location.pathname + queryString;
                                // Use pushState to set the new URL and pass params as the state object
                                window.history.pushState(params, '', newUrl);
                                loadUrlBasedOnURL();

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
            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
