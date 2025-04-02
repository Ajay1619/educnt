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
        <h2>Personal Details</h2>
        <form id="faculty-personal-profile-info-faculty-form" method="POST">
            <div class="row">
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="first-name" name="first_name" placeholder=" " value="<?= $logged_first_name ?>" required aria-required="true">
                        <label class="input-label" for="first-name">Enter Your First Name</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="middle-name" name="middle_name" placeholder=" " value="<?= $logged_middle_name ?>">
                        <label class="input-label" for="middle-name">Enter Your Middle Name</label>
                    </div>
                </div>
                <div class="col col-4 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="last-name" name="last_name" placeholder=" " value="<?= $logged_last_name ?>" aria-required="true">
                        <label class="input-label" for="last-name">Enter Your Lastname</label>
                    </div>
                </div>
                <div class="col col-1 col-lg-2 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="initial" name="initial" placeholder=" " value="<?= $logged_initial ?>" required aria-required="true">
                        <label class="input-label" for="initial">Enter Your Intial</label>
                    </div>
                </div>

                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="salutation-dummy" class="auto dropdown-input" placeholder=" " readonly required>
                            <label class="input-label" for="salutation-dummy">Select Your Salutation</label>
                            <input type="hidden" name="salutation" id="salutation">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="salutations-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container date">
                        <input type="date" value="" class="bulmaCalender" id="date-of-birth" name="date_of_birth" placeholder="dd-MM-yyyy" required aria-required="true">
                        <label class="input-label " for="date-of-birth">Select Your Date Of Birth</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="gender-dummy" name="gender" class="auto dropdown-input" placeholder=" " readonly required>
                            <label class="input-label" for="gender-dummy">Select Your Gender</label>
                            <input type="hidden" name="gender" id="gender">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="gender-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="blood-group-dummy" name="blood-group" class="auto dropdown-input" placeholder=" " readonly required>
                            <label class="input-label" for="blood-group-dummy">Select Your Blood Group</label>
                            <input type="hidden" name="blood-group" id="blood-group">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="blood-group-suggestions"></div>
                        </div>
                    </div>
                </div>


                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="aadhar-number" name="aadhar_number" placeholder=" " required aria-required="true" maxlength="14">
                        <label class="input-label" for="aadhar-number">Enter Your Aadhar number</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="religion-dummy" name="religion" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="religion-dummy">Select Your Religion</label>
                            <input type="hidden" name="religion" id="religion">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="religion-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="caste-dummy" name="caste" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="caste-dummy">Select Your Caste</label>
                            <input type="hidden" name="caste" id="caste">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="caste-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="community-dummy" name="community" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="community-dummy">Select Your Community</label>
                            <input type="hidden" name="community" id="community">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="community-suggestions"></div>
                        </div>
                    </div>
                </div>

                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="nationality-dummy" name="nationality" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="nationality-dummy">Select Your Nationality</label>
                            <input type="hidden" name="nationality" id="nationality">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="nationality-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="marital-status-dummy" name="marital-status" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="marital-status-dummy">Select Your Marital Status</label>
                            <input type="hidden" name="marital-status" id="marital-status">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="marital-status-suggestions"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-navigation">
                <button class="nav-next text-left disabled" id="personal_profile_info_faculty_form_prev_btn" type="button" disabled>Previous</button>
                <button class="nav-back text-right" id="personal_profile_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
    </div>

    <script>
        //document.ready function
        $(document).ready(async function() {
            // Calculate the date 17 years ago from today
            var today = new Date();
            var maxDate = new Date(today.setFullYear(today.getFullYear() - 17));

            // Initialize the Bulma Calendar
            // var calendars = bulmaCalendar.attach('#date-of-birth', {
            //     type: 'date',
            //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            //     validateLabel: "",
            //     maxDate: maxDate
            // });

            // Set up an event listener to update the input field
            calendars.forEach(calendar => {
                calendar.on('select', function(datepicker) {
                    document.querySelector('#date-of-birth').value = datepicker.data.value();
                });
            });
            await fetch_faculty_personal_data();


            $('#salutation-dummy').on('click focus', async function() {
                await fetch_salutations($(this));
            });
            $('#gender-dummy').on('click focus', async function() {
                await fetch_gender($(this));
            });
            $('#blood-group-dummy').on('click focus', async function() {
                await fetch_blood_group($(this));
            });
            $('#religion-dummy').on('click focus', async function() {
                await fetch_religion($(this));
            });
            $('#caste-dummy').on('click focus', async function() {
                await fetch_caste($(this));
            });
            $('#community-dummy').on('click focus', async function() {
                await fetch_community($(this));
            });
            $('#nationality-dummy').on('click focus', async function() {
                await fetch_nationality($(this));
            });
            $('#marital-status-dummy').on('click focus', async function() {
                await fetch_marital_status($(this));
            });


            $('#first-name').on('blur', function() {
                input_validation($(this));
            });
            $('#middle-name').on('blur', function() {
                input_validation($(this));
            });
            $('#last-name').on('blur', function() {
                input_validation($(this));
            });
            $('#initial').on('blur', function() {
                input_validation($(this));
            });
            $('#aadhar-number').on('blur', function() {
                input_validation($(this));
            });
            $('#aadhar-number').on('blur', function() {
                //$(this).val() length should be 14
                if ($(this).val().length !== 14) {
                    $(this).addClass('error');
                    $(this).val("");
                    showToast('error', 'Please enter a valid Aadhar number.');
                }
            });

            $('#faculty-personal-profile-info-faculty-form').on('submit', function(e) {
                e.preventDefault();
                const data = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_profile/ajax/faculty_personal_profile_info_faculty_form.php' ?>',
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
                                type: 'personal',
                                tab: 'contact'
                            };

                            // Construct the new URL with query parameters
                            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                            const newUrl = window.location.origin + window.location.pathname + queryString;
                            // Use pushState to set the new URL and pass params as the state object
                            window.history.pushState(params, '', newUrl);
                            load_personal_info_components();

                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
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
