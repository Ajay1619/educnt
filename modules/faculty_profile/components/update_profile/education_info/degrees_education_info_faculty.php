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

    <div class="tab-content active" data-tab-content="3">
        <h2>Degrees</h2>
        <input type="hidden" id="degrees-count" value="1">
        <form method="POST" id="faculty-education-degree-profile-info-faculty-form">
            <div class="degrees-list">
                <input type="hidden" name="degree_id[]" class="degree-id" value="0">
                <div class="row">
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                        <div class="input-container">
                            <input type="text" id="degree-institution-name" class="degree-institution-name" name="degree_institution_name[]" placeholder=" ">
                            <label class="input-label" for="degree-institution-name">Enter Your Institution name</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <div class="input-container dropdown-container">
                                <input type="text" id="education-degree-dummy-1" name="education_degree_dummy[]" class="auto education-degree-dummy dropdown-input" placeholder=" " readonly required>
                                <label class="input-label" for="education-degree-dummy">Select Your Degree</label>
                                <input type="hidden" name="education_degree[]" class="education-degree" id="education-degree-1">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="education-degree-suggestions"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                        <div class="input-container">
                            <div class="input-container dropdown-container">
                                <input type="text" id="education-specialization-dummy-1" name="education_specialization_dummy[]" class="auto dropdown-input education-specialization-dummy" placeholder=" " readonly required>
                                <label class="input-label" for="education-specialization-dummy">Select Your Education Specialization</label>
                                <input type="hidden" name="education_degree_specialization[]" class="education-hsc-specialization" id="education-specialization-1">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="education-specialization-suggestions"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                        <div class="input-container">
                            <input type="text" id="degree-passed-out-year" class="degree-passed-out-year" name="degree_passed_out_year[]" placeholder=" ">
                            <label class="input-label" for="degree-passed-out-year">Enter Your Passed Out Year</label>
                        </div>
                    </div>


                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                        <div class="input-container">
                            <input type="text" id="degree-percentage" class="degree-percentage" name="degree_percentage[]" placeholder=" ">
                            <label class="input-label" for="degree-percentage">Enter Your Percentage</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                        <div class="input-container">
                            <input type="text" id="degree-cgpa" class="degree-cgpa" name="degree_cgpa[]" placeholder=" ">
                            <label class="input-label" for="degree-cgpa">Enter Your CGPA</label>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="add-another-degree flex-container align-center underline">
                <span class="add-degree">Add Another degree
                    <button type="button" id="add-degree-btn" class="icon tertiary">+</button>
                </span>
            </div>
            <div class="form-navigation">
                <button class="nav-next text-left" id="education_degree_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="nav-back text-right" id="education_degree_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
        <script>
            $(document).ready(async function() {
                try {


                    await fetch_faculty_education_degrees_data();
                    //class=cancel-degree on click

                    // Event listener for adding another degree section
                    $('.add-degree').on('click', function() {
                        let degreeCount = $("#degrees-count").val();
                        degreeCount++; // Increment degree count
                        $("#degrees-count").val(degreeCount);
                        const degreeTemplate = `
                        <input type="hidden" name="degree_id[]" class="degree-id" value="0">
                            <div class="row">
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container">
                                        <input type="text" id="degree-institution-name-${degreeCount}" class="degree-institution-name"  name="degree_institution_name[]" placeholder=" " >
                                        <label class="input-label" for="degree-institution-name-${degreeCount}">Enter Your Institution name</label>
                                    </div>
                                </div>
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container">
                                        <div class="input-container dropdown-container">
                                            <input type="text" id="education-degree-dummy-${degreeCount}" name="education_degree_dummy[]" class="auto dropdown-input education-degree-dummy" placeholder=" " readonly required>
                                            <label class="input-label" for="education-degree-dummy">Select Your Degree</label>
                                            <input type="hidden" name="education_degree[]" class="education-degree" id="education-degree-${degreeCount}">
                                            <span class="dropdown-arrow">&#8964;</span>
                                            <div class="dropdown-suggestions" id="education-degree-suggestions"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container dropdown-container">
                                        <input type="text" id="education-specialization-dummy-${degreeCount}" name="education_specialization_dummy[]" class="auto dropdown-input education-specialization-dummy" placeholder=" " readonly required>
                                        <label class="input-label" for="education-specialization-dummy-${degreeCount}">Select Your Education Specialization</label>
                                        <input type="hidden" name="education_degree_specialization[]" class="education-hsc-specialization" id="education-specialization-${degreeCount}">
                                        <span class="dropdown-arrow">&#8964;</span>
                                        <div class="dropdown-suggestions" id="education-specialization-suggestions-${degreeCount}"></div>
                                    </div>
                                </div>
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container">
                                        <input type="text" id="degree-passed-out-year-${degreeCount}" class="degree-passed-out-year" name="degree_passed_out_year[]" placeholder=" " >
                                        <label class="input-label" for="degree-passed-out-year-${degreeCount}">Enter Your Passed Out Year</label>
                                    </div>
                                </div>
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container">
                                        <input type="text" id="degree-percentage-${degreeCount}" class="degree-percentage" name="degree_percentage[]" placeholder=" " >
                                        <label class="input-label" for="degree-percentage-${degreeCount}">Enter Your Percentage</label>
                                    </div>
                                </div>
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-container">
                                        <input type="text" id="degree-cgpa-${degreeCount}" class="degree-cgpa" name="degree_cgpa[]" placeholder=" " >
                                        <label class="input-label" for="degree-cgpa-${degreeCount}">Enter Your CGPA</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        `;

                        $('.degrees-list').append(degreeTemplate);

                        $('.education-degree-dummy').on('click focus', async function() {
                            await fetch_education_degrees($(this));
                        });
                        $('.education-specialization-dummy').on('click focus', async function() {
                            await fetch_education_specialization($(this));
                        });
                        $('.degree-institution-name').on('input', function() {
                            input_validation($(this));
                        });
                        $('.degree-passed-out-year').on('blur', function() {
                            input_validation($(this));
                        });
                        $('.degree-percentage').on('blur', function() {
                            input_validation($(this));
                        });
                        $('.degree-cgpa').on('blur', function() {
                            input_validation($(this));
                        });
                    });



                    $('.education-degree-dummy').on('click focus', async function() {
                        await fetch_education_degrees($(this));
                    });
                    $('.education-specialization-dummy').on('click focus', async function() {
                        await fetch_education_specialization($(this));
                    });
                    $('.degree-institution-name').on('input', function() {
                        input_validation($(this));
                    });
                    $('.degree-passed-out-year').on('blur', function() {
                        input_validation($(this));
                    });
                    $('.degree-percentage').on('blur', function() {
                        input_validation($(this));
                    });
                    $('.degree-cgpa').on('blur', function() {
                        input_validation($(this));
                    });

                    $('#education_degree_info_faculty_form_prev_btn').on('click', function() {
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
                    });

                    //id="faculty-education-degree-profile-info-faculty-form" onsubmit
                    $('#faculty-education-degree-profile-info-faculty-form').on('submit', async function(e) {
                        e.preventDefault();
                        const data = $(this).serialize();
                        $.ajax({
                            type: 'POST',
                            url: '<?= MODULES . '/faculty_profile/ajax/faculty_education_degree_profile_info_faculty_form.php' ?>',
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
                                        type: 'experience',
                                        tab: 'industry'
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
    <?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
