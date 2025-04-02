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
    <form id="faculty_student_admission_official_details_form">
        <div class="tab-content active" data-tab-content="1">

            <div class="row">

                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="official-mail-id" name="mail_id" placeholder=" ">
                        <label class="input-label" for="mailid">Enter official's Mail id</label>
                    </div>
                </div>
                <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="register-number" name="register_number" placeholder=" ">
                        <label class="input-label" for="register_number">Enter student's Register Number</label>
                    </div>
                </div>


            </div>
        </div>
        <div class="form-navigation">
            <button class="btn prev-btn" type="button" id="prev_official_btn_official">Previous</button>
            <button class="btn next-btn" id="nxt_btn_official">Next</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            fetch_official_data();
            const load_student_parent_profile_info_form = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/parent_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + '?action=add&route=faculty&type=personal&tab=parent'
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
            const fetch_student_official_data = () => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_official_data.php' ?>',
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
                            $('#alt-mobile-number').val(data.student_alternative_official_number);
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
            // fetch_student_official_data();
            // load_student_official_profile_info_form();
            $('#prev_official_btn_official').on('click', function(e) {
                showComponentLoading(1)
                // console.log("hi");
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
                loadprofileComponentsBasedOnURL();
                loadUrlBasedOnURL();
                // load_personal_info_components();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });
            $('#nxt_btn_official').on('click', function(e) {
                showComponentLoading(2)

                e.preventDefault();
                const formData = new FormData($('#faculty_student_admission_official_details_form')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/official_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
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
                            params = '?action=add&route=faculty&type=education&tab=schooling';
                            // params = '?action=add&route=faculty&type=personal&tab=parent';
                            const newUrl = window.location.origin + window.location.pathname + params;
                            console.log(window.location.origin);
                            console.log(window.location.pathname);
                            console.log(params);
                            // Use history.pushState to update the URL without refreshing the page
                            history.pushState({
                                action: 'add',
                                route: 'faculty'
                            }, '', newUrl);
                            EducationProfileForm();
                            loadUrlBasedOnURL();

                            // load_student_parent_profile_info_form();
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
