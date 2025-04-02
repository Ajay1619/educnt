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

    <section id="update_personal_profile">
        <div class="step-content active" data-step="2">
            <section id="faculty-experience">
                <h2>Experience</h2>
                <input type="hidden" id="experience-count" value="1">
                <form id="faculty-experience-profile-info-faculty-form" method="POST">
                    <div class="experience-list">
                        <input type="hidden" name="experience_id[]" class="experience-id" value="0">
                        <div class="row">
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <div class="input-container dropdown-container">
                                        <input type="text" id="field-of-experience-dummy-1" name="field_of_experience_dummy[]" class="auto field-of-experience-dummy dropdown-input" placeholder=" " readonly>
                                        <label class="input-label" for="field-of-experience-dummy">Select Your Field Of Experience</label>
                                        <input type="hidden" name="field_of_experience[]" class="field-of-experience" id="field-of-experience-1">
                                        <span class="dropdown-arrow">&#8964;</span>
                                        <div class="dropdown-suggestions" id="field-of-experience-suggestions"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-name-1" class="experience-industry-name" name="experience_industry_name[]" placeholder=" ">
                                    <label class="input-label" for="experience-industry-name">Enter Your Industry
                                        Name
                                    </label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-designation-1" class="experience-designation" name="experience_designation[]" placeholder=" ">
                                    <label class="input-label" for="experience-designation">Enter Your
                                        Designation</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-department-1" class="experience-industry-department" name="experience_industry_department[]" placeholder=" ">
                                    <label class="input-label" for="experience-industry-department">Enter Your
                                        Specialization</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-start-date" name="experience_industry_start_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>">
                                    <label class="input-label" for="experience-industry-start-date">Enter Your Timespan
                                        Start Date</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-end-date" name="experience_industry_end_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>">
                                    <label class="input-label" for="experience-industry-end-date">Enter Your Timespan
                                        End Date</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="add-another-experience flex-container align-center underline">
                        <span class="add-experience">Add Another Experience
                            <button type="button" id="add-experience-btn" class="icon tertiary">+</button>
                        </span>
                    </div>
                    <div class="form-navigation">
                        <button class="nav-next text-left" id="experience_info_faculty_form_prev_btn" type="button">Previous</button>
                        <button class="nav-back text-right" id="experience_info_faculty_form_nxt_btn" type="submit">Next</button>
                    </div>
                </form>
            </section>
        </div>
    </section>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <script>
        $(document).ready(async function() {
            try {
                await fetch_faculty_experience_data();

                var field_of_experience = [{
                        title: 'Teaching',
                        value: 1
                    },
                    {
                        title: 'Industry',
                        value: 2
                    }
                ];
                $('.field-of-experience-dummy').on('click focus', function() {
                    console.log(field_of_experience)
                    const element = $(this);
                    const suggestions = element.siblings(".dropdown-suggestions")
                    const value = element.siblings(".field-of-experience")
                    showSuggestions(field_of_experience, suggestions, value, element);


                });
                $('.experience-designation').on('input', function() {
                    input_validation($(this))
                });
                $('.experience-industry-name').on('input', function() {
                    input_validation($(this))
                });
                $('.experience-industry-department').on('input', function() {
                    input_validation($(this))
                });

                $('#experience_info_faculty_form_prev_btn').on('click', function() {
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
                });

                // Initialize the Bulma Calendar
                // Define the calendar options
                // const options = {
                //     type: 'date',
                //     dateFormat: '<?= BULMA_DATE_FORMAT ?>', // Set your preferred date format
                //     validateLabel: ""
                // };


                //const calendar = bulmaCalendar.attach('[type="date"]', options);

                $('#faculty-experience-profile-info-faculty-form').on('submit', async function(e) {
                    e.preventDefault();
                    const data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: '<?= MODULES . '/faculty_profile/ajax/faculty_experience_profile_info_faculty_form.php' ?>',
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
                                    type: 'skill',
                                    tab: 'knowledge'
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

                $('.add-experience').on('click', function() {
                    let experienceCount = $("#experience-count").val();
                    experienceCount++; // Increment experience count
                    $("#experience-count").val(experienceCount);
                    const experienceTemplate = `
                            <input type="hidden" name="experience_id[]" class="experience-id" value="0">
                            <div class="row">
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <div class="input-container dropdown-container">
                                        <input type="text" id="field-of-experience-dummy-${experienceCount}" name="field_of_experience_dummy[]" class="auto field-of-experience-dummy dropdown-input" placeholder=" " readonly >
                                        <label class="input-label" for="field-of-experience-dummy">Select Your Field Of Experience</label>
                                        <input type="hidden" name="field_of_experience[]" class="field-of-experience" id="field-of-experience-${experienceCount}">
                                        <span class="dropdown-arrow">&#8964;</span>
                                        <div class="dropdown-suggestions" id="field-of-experience-suggestions"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-name-${experienceCount}" class="experience-industry-name" name="experience_industry_name[]" placeholder=" ">
                                    <label class="input-label" for="experience-industry-name">Enter Your Industry
                                        Name
                                    </label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-designation-${experienceCount}" class="experience-designation"  name="experience_designation[]" placeholder=" ">
                                    <label class="input-label" for="experience-designation">Enter Your
                                        Designation</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-department-${experienceCount}" name="experience_industry_department[]"  class="experience-industry-department"  placeholder=" ">
                                    <label class="input-label" for="experience-industry-department">Enter Your
                                        Department</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-start-date" name="experience_industry_start_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>">
                                    <label class="input-label" for="experience-industry-start-date">Enter Your Timespan
                                        Start Date</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-end-date" name="experience_industry_end_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>">
                                    <label class="input-label" for="experience-industry-end-date">Enter Your Timespan
                                        End Date</label>
                                </div>
                            </div>
                        </div>
                                <hr>
                            `;

                    $('.experience-list').append(experienceTemplate);

                    $('.field-of-experience-dummy').on('click focus', function() {
                        const element = $(this);
                        const suggestions = element.siblings(".dropdown-suggestions")
                        const value = element.siblings(".field-of-experience")
                        showSuggestions(field_of_experience, suggestions, value, element);

                    });
                    $('.experience-designation').on('input', function() {
                        input_validation($(this))
                    });
                    $('.experience-industry-name').on('input', function() {
                        input_validation($(this))
                    });
                    $('.experience-industry-department').on('input', function() {
                        input_validation($(this))
                    });
                    // const options = {
                    //     type: 'date',
                    //     dateFormat: '<?= BULMA_DATE_FORMAT ?>', // Set your preferred date format
                    //     validateLabel: "",
                    //     closeOnSelect: true
                    // };


                    //const calendar = bulmaCalendar.attach('[type="date"]', options);

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
