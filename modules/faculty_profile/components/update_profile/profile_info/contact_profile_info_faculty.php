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
        <h2>Contact Details</h2>
        <form id="faculty-personal-contact-profile-info-faculty-form" method="POST">
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="personal-mail-id" name="personal_mail_id" placeholder=" " required aria-required="true">
                        <label class="input-label" for="personal-mail-id">Enter Your Personal Mail id</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="official-mail-id" name="official_mail_id" placeholder=" " required aria-required="true">
                        <label class="input-label" for="official-mail-id">Enter Your Official Mail id</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="mobile-number" name="mobile_number" placeholder=" " maxlength="10" required aria-required="true">
                        <label class="input-label" for="mobile-number">Enter Your Mobile Number</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="alt-mobile-number" name="alt_mobile_number" placeholder=" " maxlength="10" required aria-required="true">
                        <label class="input-label" for="alt-mobile-number">Enter Your Alternative Mobile
                            Number</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  ">
                    <div class="input-container">
                        <input type="text" id="whatsapp-mobile-number" name="whatsapp_mobile_number" maxlength="10" placeholder=" " required aria-required="true">
                        <label class="input-label" for="whatsapp-mobile-number">Enter Your Whatsapp Mobile
                            Number</label>
                    </div>
                </div>

            </div>
            <div class="form-navigation">
                <button class="nav-next text-left" id="personal_contact_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="nav-back text-right" id="personal_contact_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(async function() {
            // Previous button click event
            $('#personal_contact_info_faculty_form_prev_btn').on('click', function() {
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
                load_personal_info_components();
            });
            await fetch_faculty_contact_data();

            $('#personal-mail-id').on('blur', function() {
                input_validation($(this));
            });

            //official-mailid
            $('#official-mail-id').on('blur', function() {
                input_validation($(this));
            });

            //give validation for all inputs
            $('#mobile-number').on('blur', function() {
                input_validation($(this));
            });

            //give validation for all inputs
            $('#alt-mobile-number').on('blur', function() {
                input_validation($(this));
            });

            //give validation for all inputs
            $('#whatsapp-mobile-number').on('blur', function() {
                input_validation($(this));
            });

            $('#faculty-personal-contact-profile-info-faculty-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_personal_contact_profile_info_faculty_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: formData,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
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
                            load_personal_info_components();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
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
