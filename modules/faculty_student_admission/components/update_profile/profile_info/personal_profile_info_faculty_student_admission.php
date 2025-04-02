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
    // echo $existingAdmissionValue;
?>
    <div class="tab-content active" data-tab-content="1">
        <form id="faculty_student_admission_personal_details_form" method="POST">
            <div class="row">
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="first-name" name="first_name" placeholder=" " autocomplete="off">
                        <label class="input-label" for="first-name">Enter student's First Name</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="middle-name" name="middle_name" placeholder=" ">
                        <label class="input-label" for="middle-name">Enter student's Middle Name</label>
                    </div>
                </div>
                <div class="col col-4 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="last-name" name="last_name" placeholder=" ">
                        <label class="input-label" for="last-name">Enter student's Lastname</label>
                    </div>
                </div>
                <div class="col col-1 col-lg-2 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="initial" name="initial" placeholder=" ">
                        <label class="input-label" for="initial">Enter student's Intial</label>
                    </div>
                </div>


                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container date">
                        <input type="date" value="" class="" id="date-of-birth" name="date_of_birth" placeholder="dd-MM-yyyy">
                        <label class="input-label " for="date-of-birth">Select student's Date Of Birth</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="gender-dummy" name="genders" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="gender-dummy">Select student's Gender</label>
                            <input type="hidden" name="gender" id="gender">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="gender-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="blood-group-dummy" name="blood-groups" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="blood-group-dummy">Select student's Blood Group</label>
                            <input type="hidden" name="blood-group" id="blood-group">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="blood-group-suggestions"></div>
                        </div>
                    </div>
                </div>


                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <input type="text" id="aadhar-number" name="aadhar_number" placeholder=" " maxlength="14">
                        <label class="input-label" for="aadhar-number">Enter student's Aadhar number</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container">
                        <div class="input-container dropdown-container">
                            <input type="text" id="religion-dummy" name="religion" class="auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="religion-dummy">Select student's Religion</label>
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
                            <label class="input-label" for="caste-dummy">Select student's Caste</label>
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
                            <label class="input-label" for="community-dummy">Select student's Community</label>
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
                            <label class="input-label" for="nationality-dummy">Select student's Nationality</label>
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
                            <label class="input-label" for="marital-status-dummy">Select student's Marital Status</label>
                            <input type="hidden" name="marital_status" id="marital-status">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="marital-status-suggestions"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <input type="hidden" id="admission_student_existing" name="admission_student_existing" value="" > -->

            <div class="form-navigation">
                <button class="btn prev-btn disabled" type="button" id="prev_parent_btn_address">Previous</button>
                <button class="btn next-btn right" id="personal_profile_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {

            const fetch_gender = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_gender.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const gender = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(gender, $('#gender-suggestions'), $('#gender'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_faculty_personal_data = () => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_personal_data.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            $('#first-name').val(data.student_first_name);
                            $('#middle-name').val(data.student_middle_name);
                            $('#last-name').val(data.student_last_name);
                            $('#initial').val(data.student_initial);
                            $('#salutation-dummy').val(data.student_salutation_title);
                            $('#salutation').val(data.student_salutation);
                            $('#date-of-birth').val(data.student_dob);
                            $('#gender-dummy').val(data.student_gender_title);
                            $('#gender').val(data.student_gender);
                            $('#blood-group-dummy').val(data.student_blood_group_title);
                            $('#blood-group').val(data.student_blood_group);
                            $('#aadhar-number').val(data.student_aadhar_number);
                            $('#religion-dummy').val(data.student_religion_title);
                            $('#religion').val(data.student_religion);
                            $('#caste-dummy').val(data.student_caste_title);
                            $('#caste').val(data.student_caste);
                            $('#community-dummy').val(data.student_community_title);
                            $('#community').val(data.student_community);
                            $('#nationality-dummy').val(data.student_nationality_title);
                            $('#nationality').val(data.student_nationality);
                            $('#marital-status-dummy').val(data.student_marital_status_title);
                            $('#marital-status').val(data.student_marital_status);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            }
            const fetch_blood_group = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_blood_group.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const blood = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(blood, $('#blood-group-suggestions'), $('#blood-group'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_religion = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_religion.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const religion = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(religion, $('#religion-suggestions'), $('#religion'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_caste = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_caste.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const caste = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(caste, $('#caste-suggestions'), $('#caste'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_community = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_community.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const community = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(community, $('#community-suggestions'), $('#community'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_nationality = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_nationality.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const nationality = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(nationality, $('#nationality-suggestions'), $('#nationality'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }

            const fetch_marital_status = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_marital_status.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const marital = response.data;
                                showDropdownLoading(element.siblings(".dropdown-suggestions"))
                                showSuggestions(marital, $('#marital-status-suggestions'), $('#marital-status'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }


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
            fetch_faculty_personal_data();

            const load_student_contact_profile_info_form = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/contact_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + '?action=add&route=faculty&type=personal&tab=contact' // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                        },
                        success: function(response) {
                            $('#info').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            };
            const input_validation = (element) => {
                const name = element.attr('name');
                const id = element.attr('id');
                const value = element.val();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_profile/ajax/faculty_profile_input_validation.php' ?>',
                    data: {
                        'name': name,
                        'value': value
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code !== 200) {
                            showToast(response.status, response.message);
                            element.addClass(response.status)
                            element.val("");
                        } else {
                            element.removeClass('error');
                            element.removeClass('warning');
                            element.addClass(response.status);
                            if (name == 'first_name') {
                                $('#first-name').val(response.data);
                            }
                            if (name == 'middle_name') {
                                $('#middle-name').val(response.data);
                            }
                            if (name == 'last_name') {
                                $('#last-name').val(response.data);
                            }
                            if (name == 'initial') {
                                $('#initial').val(response.data);
                            }
                            if (name == 'aadhar_number') {
                                $('#aadhar-number').val(response.data);
                            }

                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            }
            // load_student_contact_profile_info_form();
            $('#faculty_student_admission_personal_details_form').on('submit', function(e) {
                showComponentLoading("Updating...")

                e.preventDefault();
                // const formData = new FormData($('#faculty_student_admission_personal_details_form')[0]); // Corrected to reference the form
                const data = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/personal_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: data,

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },

                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast('success', response.message);
                            load_student_contact_profile_info_form();
                            $('.tab-btn.contact').addClass('active');
                            $('.tab-btn.personal').removeClass('active');
                            params = '?action=add&route=faculty&type=personal&tab=contact';
                            const newUrl = window.location.origin + window.location.pathname + params;

                            // Use history.pushState to update the URL without refreshing the page
                            history.pushState({
                                action: 'add',
                                route: 'faculty'
                            }, '', newUrl);

                        } else {
                            console.log(response.message);
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });


        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
