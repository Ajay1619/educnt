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


    <div class="tab-content active " data-tab-content="2">
        <h2>Address Details</h2>
        <form method="POST" id="faculty-personal-address-profile-info-faculty-form">
            <div class="row">
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="number" id="address-house-number" name="address_house_number" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-house-number">Enter Your House number</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-street" name="address_street" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-street">Enter Your Street</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-locality" name="address_locality" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-locality">Enter Your Locality</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-pincode" name="address_pincode" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-pincode">Enter Your Pincode</label>
                    </div>
                </div>

                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-city" name="address_city" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-city">Enter Your City</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-district" name="address_district" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-district">Enter Your District</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-state" name="address_state" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-state">Enter Your State</label>
                    </div>
                </div>
                <div class="col col-4 col-xs-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="input-container">
                        <input type="text" id="address-country" name="address_country" placeholder=" " required aria-required="true">
                        <label class="input-label" for="address-country">Enter Your Country</label>
                    </div>
                </div>
            </div>
            <div class="form-navigation">
                <button class="nav-next text-left" id="personal_address_info_faculty_form_prev_btn" type="button">Previous</button>
                <button class="nav-back text-right" id="personal_address_info_faculty_form_nxt_btn" type="submit">Next</button>
            </div>
        </form>
    </div>


    <script>
        //give document.ready function
        $(document).ready(async function() {

            await fetch_faculty_address_data();
            $('#address-pincode').on('input', function() {
                var pincode = $('#address-pincode').val();

                if (pincode.length == 6) {
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
                            $('#address-house-number').focus();
                        },
                        error: function() {
                            $('#billing-address-details').html('<p>Invalid pincode or no data available.</p>');
                        }
                    });
                } else {
                    $('#billing-address-details').html('<p>Please enter a pincode.</p>');
                }
            });
            

        });

        //address_street on input
        $('#address-street').on('blur', function() {
            input_validation($(this));
        });
        //address_locality on input
        $('#address-locality').on('blur', function() {
            input_validation($(this));
        });
        //address_pincode on input

        //address_city on input
        $('#address-city').on('blur', function() {
            input_validation($(this));
        });
        //address_district on input
        $('#address-district').on('blur', function() {
            input_validation($(this));
        });
        //address_state on input
        $('#address-state').on('blur', function() {
            input_validation($(this));
        });
        //address_country on input
        $('#address-country').on('blur', function() {
            input_validation($(this));
        });

        // id=personal_address_info_faculty_form_prev_btn onclick
        $('#personal_address_info_faculty_form_prev_btn').on('click', function() {
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
        });
        //id="faculty-personal-address-profile-info-faculty-form" onsubmit 
        $('#faculty-personal-address-profile-info-faculty-form').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/faculty_profile/ajax/faculty_personal_address_profile_info_faculty_form.php' ?>',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        showToast(response.status, response.message);
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
                        load_update_profile_components();
                    } else {
                        showToast('error', data.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error occured while submitting the form.');
                }
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
