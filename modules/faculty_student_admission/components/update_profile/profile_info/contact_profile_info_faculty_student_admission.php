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
    <form id="faculty_student_admission_contact_details_form">
        <div class="tab-content active" data-tab-content="1">

            <div class="row">

                <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="personal-mail-id" name="mail_id" placeholder=" " required aria-required="true">
                        <label class="input-label" for="mailid">Enter student's Mail id</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="mobile-number" name="phone_number" placeholder=" " maxlength="10" required aria-required="true">
                        <label class="input-label" for="Phone_number">Enter student's Phone Number</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="alt-mobile-number" name="alt_phone_number" placeholder=" " maxlength="10" required aria-required="true">
                        <label class="input-label" for="alt_phone_number">Enter Alternative Phone
                            Number</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="whatsapp-mobile-number" name="whatsapp_number" placeholder=" " maxlength="10" required aria-required="true">
                        <label class="input-label" for="whatsapp-number">Enter student's Whatsapp Number</label>
                    </div>
                </div>
                <input type="hidden" id="admission_student_existing" name="admission_student_existing" value="<?php echo $existingAdmissionValue; ?>" required aria-required="true">

            </div>
        </div>
        <div class="form-navigation">
            <button class="btn prev-btn" type="button" id="prev_contact_btn_contact">Previous</button>
            <button class="btn next-btn" id="nxt_btn_contact">Next</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            const load_student_parent_profile_info_form = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/parent_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + '?action=add&route=faculty&type=personal&tab=parent' // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
            const fetch_student_contact_data = () => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_contact_data.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            console.log(data);
                            $('#personal-mail-id').val(data.student_email_id);
                            $('#mobile-number').val(data.student_mobile_number);
                            $('#alt-mobile-number').val(data.student_alternative_contact_number);
                            $('#whatsapp-mobile-number').val(data.student_whatsapp_number);

                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }

                });
            }
            $('#personal-mail-id').on('blur', function() {
                input_student_validation($(this));
            });


            //give validation for all inputs
            $('#mobile-number').on('blur', function() {
                input_student_validation($(this));
            });

            //give validation for all inputs
            $('#alt-mobile-number').on('blur', function() {
                input_student_validation($(this));
            });

            //give validation for all inputs
            $('#whatsapp-mobile-number').on('blur', function() {
                input_student_validation($(this));
            });
            fetch_student_contact_data();
            // load_student_contact_profile_info_form();
            $('#prev_contact_btn_contact').on('click', function(e) {
                showComponentLoading(1)
                // console.log("hi");
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'personal',
                    tab: 'personal'
                };

                // Construct the new URL with query parameters
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                // Use pushState to set the new URL and pass params as the state object
                window.history.pushState(params, '', newUrl);
                loadprofileComponentsBasedOnURL();
                // load_personal_info_components();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });
            $('#nxt_btn_contact').on('click', function(e) {
                showComponentLoading(2)
                e.preventDefault();
                const formData = new FormData($('#faculty_student_admission_contact_details_form')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/contact_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
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
                            console.log(response);

                            $('.tab-btn.parent').addClass('active');
                            $('.tab-btn.contact').removeClass('active');
                            params = '?action=add&route=faculty&type=personal&tab=parent';
                            const newUrl = window.location.origin + window.location.pathname + params;
                            console.log(window.location.origin);
                            console.log(window.location.pathname);
                            console.log(params);
                            // Use history.pushState to update the URL without refreshing the page
                            history.pushState({
                                action: 'add',
                                route: 'faculty'
                            }, '', newUrl);
                            load_student_parent_profile_info_form();
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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
