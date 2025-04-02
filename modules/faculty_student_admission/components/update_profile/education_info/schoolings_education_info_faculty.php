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

    <div class="tab-content active" data-tab-content="0">

        <form method="POST" id="faculty-education-schoolings-profile-info-faculty-form">

            <div class='section-header-title text-left m-6'>SSLC Education</div>
            <div class="row">
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="sslc-institution-name" name="sslc_institution_name" placeholder=" ">
                        <label class="input-label" for="sslc-institution-name">Enter Student Institution Name</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="education-sslc-board-dummy" name="education_board_dummy" class="auto education-board-dummy dropdown-input" placeholder=" " readonly required>
                            <label class="input-label" for="education-board-dummy">Select Student Education Board</label>
                            <input type="hidden" name="education_board" class="education-board" id="education-sslc-board">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="education-board-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="sslc-passed-out-year" name="sslc_passed_out_year" placeholder=" ">
                        <label class="input-label" for="sslc-passed-out-year">Enter Student Passed Out Year</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="sslc-mark" name="sslc_mark" placeholder=" ">
                        <label class="input-label" for="sslc-mark">Enter Student Mark</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="sslc-percentage" name="sslc_percentage" placeholder=" ">
                        <label class="input-label" for="sslc-percentage">Enter Student Percentage</label>
                    </div>
                </div>
            </div>

            <div class='section-header-title text-left m-6'>HSC Education</div>
            <div class="row">
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="hsc-institution-name" name="hsc_institution_name" placeholder=" ">
                        <label class="input-label" for="hsc-institution-name">Enter Student Institution name</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <div class="input-container">
                            <div class="input-container dropdown-container">
                                <input type="text" id="education-hsc-board-dummy" name="education_board_dummy" class="auto education-board-dummy dropdown-input" placeholder=" " readonly required>
                                <label class="input-label" for="education-board-dummy">Select Student Education Board</label>
                                <input type="hidden" name="education_hsc_board" class="education-board" id="education-hsc-board">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="education-board-suggestions"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="education-hsc-specialization-dummy" name="education_specialization_dummy" class="auto dropdown-input education-specialization-dummy" placeholder=" " readonly required>
                            <label class="input-label" for="education-specialization-dummy">Select Student Education Specialization</label>
                            <input type="hidden" name="education_hsc_specialization" id="education-hsc-specialization" class="education-hsc-specialization">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="education-specialization-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="hsc-passed-out-year" name="hsc_passed_out_year" placeholder=" ">
                        <label class="input-label" for="hsc-passed-out-year">Enter Student Passed Out Year</label>
                    </div>
                </div>

                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="hsc-mark" name="hsc_mark" placeholder=" ">
                        <label class="input-label" for="hsc-mark">Enter Student Mark</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="hsc-percentage" name="hsc_percentage" placeholder=" ">
                        <label class="input-label" for="hsc-percentage">Enter Student Percentage</label>
                    </div>
                </div>

            </div>
            <input type="hidden" id="admission_student_existing" name="admission_student_existing" value="<?php echo $existingAdmissionValue; ?>">

            <div class="form-navigation">
                <button class="btn prev-btn text-left" id="education_schoolings_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="btn next-btn text-right" id="education_schoolings_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>

        <script>
            //document.ready function with async
            $(document).ready(async function() {
                try {
                    await fetch_faculty_education_schoolings_data();
                    $('.education-board-dummy').on('click focus', async function() {
                        await fetch_education_board($(this));
                    });


                    $('.education-specialization-dummy').on('click focus', async function() {
                        await fetch_education_specialization($(this));
                    });
                    $('#sslc-institution-name, #sslc-passed-out-year, #sslc-mark, #sslc-percentage').on('blur', function() {
                        input_student_validation($(this));
                    });

                    $('#hsc-institution-name, #hsc-passed-out-year, #hsc-mark, #hsc-percentage').on('blur', function() {
                        input_student_validation($(this));
                    });

                    $('#education-sslc-board-dummy, #education-hsc-board-dummy').on('blur', function() {
                        input_student_validation($(this));
                    });

                    $('#education-hsc-specialization-dummy').on('blur', function() {
                        input_student_validation($(this));
                    });

                    //education_sslc_info_faculty_form_prev_btn on click
                    $('#education_schoolings_info_faculty_form_prev_btn').on('click', async function() {
                        showComponentLoading(1)
                        const params = {
                            action: 'add',
                            route: 'faculty',
                            type: 'personal',
                            tab: 'official'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        //load_personal_info_components();
                        await loadUrlBasedOnURL()
                        setTimeout(function() {
                            hideComponentLoading();
                        }, 100)
                    });

                    //id="faculty-education-sslc-profile-info-faculty-form" onsubmit
                    $('#faculty-education-schoolings-profile-info-faculty-form').on('submit', async function(e) {
                        showComponentLoading(1)
                        e.preventDefault();
                        const data = $(this).serialize();
                        $.ajax({
                            type: 'POST',
                            url: '<?= MODULES . '/faculty_student_admission/ajax/faculty_education_schoolings_profile_info_faculty_form.php' ?>',
                            data: data,
                            headers: {
                                'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                            },
                            success: function(response) {
                                response = JSON.parse(response);
                                if (response.code == 200) {
                                    showToast(response.status, response.message);
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
                                    console.log(newUrl);
                                    window.history.pushState(params, '', newUrl);
                                    load_update_education_profile_components();

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
    </div>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
