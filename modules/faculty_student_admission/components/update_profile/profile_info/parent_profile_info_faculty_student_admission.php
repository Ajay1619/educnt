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
    //validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    //checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <form id="faculty_student_admission_parent_details_form">
        <div class="tab-content active" data-tab-content="2">


            <!-- Father Details Row -->

            <div class='section-header-title text-left m-6'>Father Details</div>
            <div class="row">
                <!-- Father Name -->
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="father-name" name="father_name" placeholder=" ">
                        <label class="input-label" for="father-name">Enter student's Father's Name</label>
                    </div>
                </div>

                <!-- Father Mobile Number -->
                <!-- <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="input-container">
                <input type="text" id="father_mobile" placeholder=" " >
                <label class="input-label" for="father_mobile">Enter student's Father's Mobile Number</label>
            </div>
        </div> -->

                <!-- Father Occupation -->
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="father-occupation" name="father_occupation" placeholder=" ">
                        <label class="input-label" for="father-occupation">Enter student's Father's Occupation</label>
                    </div>
                </div>
            </div>

            <!-- Mother Details Row -->
            <div class='section-header-title text-left m-6'>Mother Details</div>
            <!-- Mother Name -->
            <div class="row">
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="mother-name" name="mother_name" placeholder=" ">
                        <label class="input-label" for="mother-name">Enter student's Mother's Name</label>
                    </div>
                </div>

                <!-- Mother Mobile Number -->
                <!-- <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="input-container">
                <input type="text" id="mother_mobile" placeholder=" " >
                <label class="input-label" for="mother_mobile">Enter student's Mother's Mobile Number</label>
            </div>
        </div> -->

                <!-- Mother Occupation -->
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="mother-occupation" name="mother_occupation" placeholder=" ">
                        <label class="input-label" for="mother-occupation">Enter student's Mother's Occupation</label>
                    </div>
                </div>
            </div>

            <!-- Guardian Details Row -->
            <div class='section-header-title text-left m-6'>Guardian Details</div>
            <!-- Guardian Name -->
            <div class="row">
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="guardian-name" name="guardian_name" placeholder=" " aria-required="true">
                        <label class="input-label" for="guardian-name">Enter student's Guardian's Name</label>
                    </div>
                </div>

                <!-- Guardian Mobile Number -->
                <!-- <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="input-container">
                <input type="text" id="guardian_mobile" placeholder=" " >
                <label class="input-label" for="guardian_mobile">Enter student's Guardian's Mobile Number</label>
            </div>
        </div> -->

                <!-- Guardian Occupation -->
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="guardian-occupation" name="guardian_occupation" placeholder=" " aria-required="true">
                        <label class="input-label" for="guardian-occupation">Enter student's Guardian's Occupation</label>
                    </div>
                </div>
            </div>
            <input type="hidden" id="admission-student_existing" name="admission_student_existing" value="<?php echo $existingAdmissionValue; ?>">
            <div class="form-navigation">
                <button class="btn prev-btn" type="button" id="prev_parent_btn_address">Previous</button>
                <button class="btn next-btn" id="nxt_btn">Next</button>
            </div>


        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('#mother-name').on('blur', function() {
                input_validation($(this));
            });
            $('#father-name').on('blur', function() {
                input_validation($(this));
            });
            $('#guardian-name').on('blur', function() {
                input_validation($(this));
            });
            const input_validation = (element) => {
                const name = element.attr('name');
                const id = element.attr('id');
                const value = element.val();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_student_admission/ajax/student_profile_input_validation.php' ?>',
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
                            element.addClass(response.status);
                            if (name == 'father_name') {
                                $('#father-name').val(response.data);
                            }
                            if (name == 'mother_name') {
                                $('#mother-name').val(response.data);
                            }
                            if (name == 'guardian_name') {
                                $('#guardian-name').val(response.data);
                            }


                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            }
            const load_student_address_profile_info_form = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/address_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + '?action=add&route=faculty&type=personal&tab=address' // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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

            $('#prev_parent_btn_address').on('click', function(e) {
                showComponentLoading(1)
                // console.log("hi");
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
                loadprofileComponentsBasedOnURL();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });

            const fetch_student_contact_data = () => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_parent_data.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            // console.log(data);

                            $('#father-name').val(data.student_father_name);
                            $('#father-occupation').val(data.student_father_occupation);
                            $('#mother-name').val(data.student_mother_name);
                            $('#mother-occupation').val(data.student_mother_occupation);
                            $('#guardian-name').val(data.student_guardian_name);
                            $('#guardian-occupation').val(data.student_guardian_occupation);
                        } else {
                            showToast(response.status, response.message);
                        }
                    }
                });
            };

            fetch_student_contact_data();
            // load_student_contact_profile_info_form();
            $('#nxt_btn').on('click', function(e) {
                showComponentLoading(2)

                e.preventDefault();
                const formData = new FormData($('#faculty_student_admission_parent_details_form')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/parent_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },

                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            console.log('sucess');
                            showToast('success', response.message);
                            // console.log(response);

                            load_student_address_profile_info_form();
                            $('.tab-btn.address').addClass('active');
                            $('.tab-btn.parent').removeClass('active');

                            params = '?action=add&route=faculty&type=personal&tab=address';
                            const newUrl = window.location.origin + window.location.pathname + params;
                            console.log(newUrl);
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

            //             const existingId = $('#existing-id').val().trim(); // Get the value of existing-id

            // $.ajax({
            //     type: 'POST',
            //     url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/json/personal_info_fetch.php', ENT_QUOTES, 'UTF-8') ?>',
            //     data: {
            //         existing_id: existingId
            //     }, // Send existing_id directly
            //     headers: {
            //         'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
            //     },

            //     success: function(response) {
            //         response = JSON.parse(response);
            //         if (response.code == 200) {
            //             console.log('Success');
            //             showToast('success', response.message);
            //             // Populate the form with the fetched data, if needed
            //         } else {
            //             console.log(response.message);
            //             showToast(response.status, response.message);
            //         }
            //     },
            //     error: function(jqXHR) {
            //         const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //         showToast('error', message);
            //     },
            //     complete: function() {
            //         $("#Loading").html(""); // Hide loading after completion
            //     }
            // });
        });

        // var calendars = new bulmaCalendar('#date-of-birth', {
        //     type: 'date',
        //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
        //     validateLabel: ""
        // });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>