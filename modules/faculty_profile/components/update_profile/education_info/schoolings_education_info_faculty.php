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
            <h2>SSLC Education</h2>
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="sslc-institution-name" name="sslc_institution_name" placeholder=" " required aria-required="true">
                        <label class="input-label" for="sslc-institution-name">Enter Your Institution Name</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="education-sslc-board-dummy" name="education_board_dummy" class="auto education-board-dummy dropdown-input" placeholder=" " readonly required>
                            <label class="input-label" for="education-board-dummy">Select Your Education Board</label>
                            <input type="hidden" name="education_board" class="education-board" id="education-sslc-board">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="education-board-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="sslc-passed-out-year" name="sslc_passed_out_year" placeholder=" " required aria-required="true">
                        <label class="input-label" for="sslc-passed-out-year">Enter Your Passed Out Year</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="sslc-percentage" name="sslc_percentage" placeholder=" " required aria-required="true">
                        <label class="input-label" for="sslc-percentage">Enter Your Percentage</label>
                    </div>
                </div>
            </div>
            <h2>HSC Education</h2>
            <div class="row">
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="hsc-institution-name" name="hsc_institution_name" placeholder=" " required aria-required="true">
                        <label class="input-label" for="hsc-institution-name">Enter Your Institution name</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <div class="input-container">
                            <div class="input-container dropdown-container">
                                <input type="text" id="education-hsc-board-dummy" name="education_board_dummy" class="auto education-board-dummy dropdown-input" placeholder=" " readonly required>
                                <label class="input-label" for="education-board-dummy">Select Your Education Board</label>
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
                            <label class="input-label" for="education-specialization-dummy">Select Your Education Specialization</label>
                            <input type="hidden" name="education_hsc_specialization" id="education-hsc-specialization" class="education-hsc-specialization">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="education-specialization-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12   ">
                    <div class="input-container">
                        <input type="text" id="hsc-passed-out-year" name="hsc_passed_out_year" placeholder=" " required aria-required="true">
                        <label class="input-label" for="hsc-passed-out-year">Enter Your Passed Out Year</label>
                    </div>
                </div>

                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="hsc-percentage" name="hsc_percentage" placeholder=" " required aria-required="true">
                        <label class="input-label" for="hsc-percentage">Enter Your Percentage</label>
                    </div>
                </div>

            </div>
            <div class="form-navigation">
                <button class="nav-next text-left " id="education_schoolings_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="nav-back text-right" id="education_schoolings_info_faculty_form_nxt_btn" type="submit">Next</button>
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
                    $('#sslc-institution-name').on('input', function() {
                        input_validation($(this));
                    });
                    $('#sslc-passed-out-year').on('blur', function() {
                        input_validation($(this));
                    });
                    $('#sslc-percentage').on('blur', function() {
                        input_validation($(this));
                    });

                    $('#hsc-institution-name').on('input', function() {
                        input_validation($(this));
                    });
                    $('#hsc-passed-out-year').on('blur', function() {
                        input_validation($(this));
                    });
                    $('#hsc-percentage').on('blur', function() {
                        input_validation($(this));
                    });
                    //education_sslc_info_faculty_form_prev_btn on click
                    $('#education_schoolings_info_faculty_form_prev_btn').on('click', function() {
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
                        load_update_profile_components()
                    });

                    //id="faculty-education-sslc-profile-info-faculty-form" onsubmit
                    $('#faculty-education-schoolings-profile-info-faculty-form').on('submit', async function(e) {
                        e.preventDefault();
                        const data = $(this).serialize();
                        $.ajax({
                            type: 'POST',
                            url: '<?= MODULES . '/faculty_profile/ajax/faculty_education_schoolings_profile_info_faculty_form.php' ?>',
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
                                        tab: 'degrees'
                                    };

                                    // Construct the new URL with query parameters
                                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                                    const newUrl = window.location.origin + window.location.pathname + queryString;
                                    // Use pushState to set the new URL and pass params as the state object
                                    window.history.pushState(params, '', newUrl);
                                    load_update_profile_components();

                                } else {
                                    showToast(response.status, response.message);
                                }
                            },
                            error: function(error) {
                                showToast('error', 'Something went wrong. Please try again later.');
                            }
                        });
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
