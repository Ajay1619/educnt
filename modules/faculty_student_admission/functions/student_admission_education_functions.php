<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

?>
    <script>
      
        

        const load_schoolings_education_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/education_info/schoolings_education_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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
        const load_degrees_education_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/education_info/degrees_education_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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

        const fetch_education_board = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_education_board.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const board = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".education-board")
                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(board, suggestions, value, element);
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

        const fetch_education_degrees = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_education_degrees.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const degrees = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".education-degree")
                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(degrees, suggestions, value, element);
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
       

        const fetch_education_specialization = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_education_specialization.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const specialization = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".education-hsc-specialization")
                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(specialization, suggestions, value, element);
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

        const fetch_faculty_education_schoolings_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_faculty_education_schoolings_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const sslc_data = response.data[0][0];
                        const hsc_data = response.data[1][0];
                        $('#sslc-institution-name').val(sslc_data.sslc_institution_name);
                        $('#education-sslc-board').val(sslc_data.education_board);
                        $('#education-sslc-board-dummy').val(sslc_data.board_title);
                        $('#sslc-passed-out-year').val(sslc_data.sslc_passed_out_year);
                        $('#sslc-percentage').val(sslc_data.sslc_percentage);
                        $('#sslc-mark').val(sslc_data.sslc_mark);

                        $('#hsc-institution-name').val(hsc_data.hsc_institution_name);
                        $('#education-hsc-specialization').val(hsc_data.specialization);
                        $('#education-hsc-specialization-dummy').val(hsc_data.specialization_title);
                        $('#education-hsc-board-dummy').val(hsc_data.board_title);
                        $('#education-hsc-board').val(hsc_data.education_board);
                        $('#hsc-passed-out-year').val(hsc_data.hsc_passed_out_year);
                        $('#hsc-percentage').val(hsc_data.hsc_percentage);
                        $('#hsc-mark').val(hsc_data.hsc_mark);

                    } else if (response.code == 302) {
                        console.error("No data found");
                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }


        const fetch_faculty_education_degrees_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_faculty_education_degrees_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.code == 200) {
                        const data = response.data;
                        console.log(data);
                        let degreeCount = 0; // Initialize diploma count
                        var degreeTemplate = "";
                        // Loop through each degree entry in the response data
                        data.forEach(degree => {
                degreeCount++; // Increment degree count
                $("#degrees-count").val(degreeCount);

                degreeTemplate += `
                <input type="hidden" name="degree_id[]" class="degree-id" value="${degree.student_edu_id || ''}">
                <div class="row">
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="degree-institution-name-${degreeCount}" class="degree-institution-name"  name="degree_institution_name[]" placeholder=" " value="${degree.student_edu_institution_name || ''}">
                            <label class="input-label" for="degree-institution-name-${degreeCount}">Enter Your Institution name</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <div class="input-container dropdown-container">
                                <input type="text" id="education-degree-dummy-${degreeCount}" name="education_degree_dummy[]" class="auto dropdown-input education-degree-dummy" placeholder=" "  value="${degree.degree_title || ''}" readonly required>
                                <label class="input-label" for="education-degree-dummy">Select Your Degree</label>
                                <input type="hidden" name="education_degree[]" class="education-degree" value="${degree.student_edu_degree || ''}" id="education-degree-${degreeCount}">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="education-degree-suggestions"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="education-specialization-dummy-${degreeCount}" name="education_specialization_dummy[]" class="auto dropdown-input education-specialization-dummy" placeholder=" " value="${degree.specialization_title || ''}" readonly required>
                            <label class="input-label" for="education-specialization-dummy-${degreeCount}">Select Your Education Specialization</label>
                            <input type="hidden" name="education_degree_specialization[]" class="education-hsc-specialization" id="education-specialization-${degreeCount}" value="${degree.student_edu_specialization || ''}" >
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions" id="education-specialization-suggestions-${degreeCount}"></div>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="degree-passed-out-year-${degreeCount}" class="degree-passed-out-year" name="degree_passed_out_year[]" placeholder=" " value="${degree.student_edu_passed_out_year || ''}"  >
                            <label class="input-label" for="degree-passed-out-year-${degreeCount}">Enter Your Passed Out Year</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="degree-percentage-${degreeCount}" class="degree-percentage" name="degree_percentage[]" placeholder=" " value="${degree.student_edu_percentage || ''}" >
                            <label class="input-label" for="degree-percentage-${degreeCount}">Enter Your Percentage</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="text" id="degree-cgpa-${degreeCount}" class="degree-cgpa" name="degree_cgpa[]" placeholder=" "  value="${degree.student_edu_cgpa || ''}">
                            <label class="input-label" for="degree-cgpa-${degreeCount}">Enter Your CGPA</label>
                        </div>
                    </div>
                </div>
                <hr>
                `;
            });

                        $('.degrees-list').html(degreeTemplate);
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
                    } else if (response.code == 302) {

                        console.error("No data found");
                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
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
                        if (name == 'first_name' || name == 'middle_name' || name == 'last_name' || name == 'initial' || name == 'address_street' || name == 'address_locality' || name == 'address_city' || name == 'address_district' || name == 'address_state' || name == 'address_country' || name == 'aadhar_number' || name == 'official_mail_id' || name == 'personal_mail_id' || name == 'mobile_number' || name == 'alt_mobile_number' || name == 'whatsapp_mobile_number' || name == 'sslc_institution_name' || name == 'hsc_institution_name' || name == 'degree_institution_name[]' || name == 'experience_designation[]' || name == 'experience_industry_department[]' || name == 'experience_industry_name[]') {
                            element.val(response.data);
                        }



                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }
        const input_student_validation = (element) => {
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
                        element.removeClass('warning');
                        element.addClass(response.status);
                        if (name == 'first_name' || name == 'middle_name' || name == 'last_name' || name == 'initial' || name == 'address_street' || name == 'address_locality' || name == 'address_city' || name == 'address_district' || name == 'address_state' || name == 'address_country' || name == 'aadhar_number' || name == 'official_mail_id' || name == 'personal_mail_id' || name == 'mobile_number' || name == 'alt_mobile_number' || name == 'whatsapp_mobile_number' || name == 'sslc_institution_name' || name == 'hsc_institution_name' || name == 'degree_institution_name[]' || name == 'experience_designation[]' || name == 'experience_industry_department[]' || name == 'experience_industry_name[]') {
                            element.val(response.data);
                        }



                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }


        const load_update_education_profile_components = () => {
            
            const urlParams = new URLSearchParams(window.location.search);
            const route = urlParams.get('route');
            const action = urlParams.get('action');
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');

            // Condition to load the correct form based on URL parameters
            if (action == 'add' && route == 'faculty' && type == 'education') {
                if (tab == 'schooling') {
                    console.log('edu');
                    load_schoolings_education_profile_info_form();
                    $('.tab-btn.schools').addClass('active');
                    $('.tab-btn.degrees').removeClass('active');
                } else if (tab == 'degree') {
                        console.log('deg');
                    load_degrees_education_info_form();
                    $('.tab-btn.schools').removeClass('active');
                    $('.tab-btn.degrees').addClass('active');
                } else {
                    console.log('No matching condition for route and action');
                }

            }
        }


       
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
