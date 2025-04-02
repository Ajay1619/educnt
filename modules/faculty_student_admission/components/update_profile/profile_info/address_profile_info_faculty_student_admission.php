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

    <form id="faculty_student_admission_address_details_form">
        <div class="tab-content active " data-tab-content="3">

            <div class="row">
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-Pincode" name="address_pincode" placeholder=" " maxlength="6" required aria-required="true">
                        <label class="input-label" for="Pincode">Enter student's Pincode</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-House-number" name="address_house_number" placeholder=" " required aria-required="true">
                        <label class="input-label" for="Address-House-number">Enter student's House number</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-Street" name="address_street" placeholder=" " required aria-required="true">
                        <label class="input-label" for="Street">Enter student's Street</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-Locality" name="address_locality" placeholder=" " required aria-required="true">
                        <label class="input-label" for="Locality">Enter student's Locality</label>
                    </div>
                </div>

                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" name="address_city" id="Address-City" placeholder=" " required aria-required="true">
                        <label class="input-label" for="City">Enter student's City</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-District" name="address_district" placeholder=" " required aria-required="true">
                        <label class="input-label" for="District">Enter student's District</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-State" name="address_state" placeholder=" " required aria-required="true">
                        <label class="input-label" for="State">Enter student's State</label>
                    </div>
                </div>
                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="input-container">
                        <input type="text" id="Address-Country" name="address_country" placeholder=" " required aria-required="true">
                        <label class="input-label" for="Country">Enter student's Country</label>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="admission_student_existing" name="admission_student_existing" value="<?php echo $existingAdmissionValue; ?>" required aria-required="true">

        <div class="form-navigation">
            <button class="btn prev-btn" type="button" id="prev_address_btn_address">Previous</button>
            <button class="btn next-btn" id="nxt_btn_address">Next</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {

            $('#Address-Pincode, #Address-House-number, #Address-Street, #Address-Locality, #Address-City, #Address-District, #Address-State, #Address-Country').on('blur', function() {
                input_student_validation($(this));
            });

            $('#address-pincode').on('input', function() {
                var pincode = $('#address-pincode').val();

                if (pincode) {
                    $.ajax({
                        url: 'https://api.postalpincode.in/pincode/' + pincode, // Use 'in' for India. Change the country code as needed.
                        method: 'GET',
                        success: function(data) {
                            if (data[0].Status == "Success") {
                                var place = data[0].PostOffice[0];
                                $('#address-city').val(place.Name);
                                $('#address-district').val(place.District);
                                $('#address-state').val(place.State);
                                $('#address-country').val(place.Country);

                            } else {
                                showToast('warning', 'No Data Available For The Entered Pincode.');
                            }
                        },
                        error: function() {
                            $('#billing-address-details').html('<p>Invalid pincode or no data available.</p>');
                        }
                    });
                } else {
                    $('#billing-address-details').html('<p>Please enter a pincode.</p>');
                }
            });

            $('#Address-Pincode').on('input', function() {
                var pincode = $('#Address-Pincode').val();

                // Check if the length of the pincode is exactly 6 digits
                if (pincode.length == 6) {
                    // Perform the AJAX request
                    $.ajax({
                        url: 'https://api.postalpincode.in/pincode/' + pincode, // Use 'in' for India. Change the country code as needed.
                        method: 'GET',
                        success: function(data) {
                            if (data[0].Status == "Success") {
                                var place = data[0].PostOffice[0];
                                console.log(place);
                                $('#Address-City').val(place.Name);
                                $('#Address-District').val(place.District);
                                $('#Address-State').val(place.State);
                                $('#Address-Country').val(place.Country);
                            } else {
                                $('#address-details').html('<p>No data available for this pincode.</p>');
                            }

                            // Move focus to the house number input
                            $('#Address-House-number').focus();
                        },
                        error: function() {
                            $('#address-details').html('<p>Invalid pincode or no data available.</p>');
                        }
                    });
                } else if (pincode.length > 6) {
                    $('#address-details').html('<p>Please enter a valid 6-digit pincode.</p>');
                }
            });




            const EducationProfileForm = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/education_info/education_information_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token
                        },
                        success: function(response) {
                            $('#content').html(response);
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
            const fetch_faculty_address_data = () => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_address_data.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            console.log(data);
                            if (data != "") {


                                $('#Address-House-number').val(data.student_address_no);
                                $('#Address-Street').val(data.student_address_street);
                                $('#Address-Locality').val(data.student_address_locality);
                                $('#Address-Pincode').val(data.student_address_pincode);
                                $('#Address-City').val(data.student_address_city);
                                $('#Address-District').val(data.student_address_district);
                                $('#Address-State').val(data.student_address_state);
                                $('#Address-Country').val(data.student_address_country);
                            }
                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            }
            fetch_faculty_address_data();
            // load_student_contact_profile_info_form();
            $('#prev_address_btn_address').on('click', async function(e) {
                showComponentLoading(1)
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'personal',
                    tab: 'parent'
                };

                // Construct the new URL with query parameters
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                // Use pushState to set the new URL and pass params as the state object
                window.history.pushState(params, '', newUrl);
                await loadprofileComponentsBasedOnURL();
                // load_personal_info_components();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });

            $('#nxt_btn_address').on('click', function(e) {
                showComponentLoading(2)
                e.preventDefault();
                const formData = new FormData($('#faculty_student_admission_address_details_form')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/address_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },

                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            console.log('success');
                            showToast('success', response.message);
                            console.log(response);

                            // EducationProfileForm();
                            // load_sslc_education_profile_info_form();
                            $('.tab-btn.official').addClass('active');
                            $('.tab-btn.address').removeClass('active');
                            // params = '?action=add&route=faculty&type=education&tab=schoolings';
                            params = '?action=add&route=faculty&type=personal&tab=official';
                            const newUrl = window.location.origin + window.location.pathname + params;
                            // console.log(newUrl);
                            // Use history.pushState to update the URL without refreshing the page
                            history.pushState({
                                action: 'add',
                                route: 'faculty'
                            }, '', newUrl);
                            load_student_official_profile_info_form();
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
