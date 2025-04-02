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
        const StepperProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/layout/stepper_update_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#stepper').html(response);
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

        const PersonalProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_profile/components/update_profile/profile_info/personal_information_faculty.php' ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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
        const PersonalfeesForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_profile/components/update_profile/profile_info/personal_information_faculty.php' ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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

        const EducationProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/education_info/education_information_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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
        const ExperiencesProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/experience_info/experience_information_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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
        const SkillProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/skill_knowledge_info/skill_information_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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
        const UploadProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/upload_file_info/upload_information_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
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

        const load_personal_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/profile_info/personal_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
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

        const load_contact_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/profile_info/contact_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + '?action=add&route=faculty&type=personal&tab=contact' // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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

        const load_address_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/profile_info/address_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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

        const load_official_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile/profile_info/official_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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

        const fetch_salutations = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_salutations.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const salutation = response.data;
                            showSuggestions(salutation, $('#salutations-suggestions'), $('#salutation'), element);
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
        const fetch_faculty_designation = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_faculty_designation.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const faculty_designation = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".faculty-designation")
                            showSuggestions(faculty_designation, suggestions, value, element);
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
        }

        const fetch_dept_list = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const dept_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".faculty-dept")
                            showSuggestions(dept_list, suggestions, value, element);
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
                        if (name == 'first_name' || name == 'middle_name' || name == 'last_name' || name == 'initial' || name == 'address_street' || name == 'address_locality' || name == 'address_city' || name == 'address_district' || name == 'address_state' || name == 'address_country' || name == 'aadhar_number' || name == 'official_mail_id' || name == 'personal_mail_id' || name == 'mobile_number' || name == 'alt_mobile_number' || name == 'whatsapp_mobile_number' || name == 'sslc_institution_name' || name == 'hsc_institution_name' || name == 'degree_institution_name[]' || name == 'experience_designation[]' || name == 'experience_industry_department[]' || name == 'experience_industry_name[]' || name == 'skills_input' || name == 'software_skills_input' || name == 'interest_input' || name == 'languages_input') {
                            element.val(response.data);
                        }



                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }

        const fetch_faculty_personal_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_personal_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        
                        $('#first-name').val(data.faculty_first_name);
                        $('#middle-name').val(data.faculty_middle_name);
                        $('#last-name').val(data.faculty_last_name);
                        $('#initial').val(data.faculty_initial);
                        $('#salutation-dummy').val(data.faculty_salutation_title);
                        $('#salutation').val(data.faculty_salutation);
                        $('#date-of-birth').val(data.faculty_dob);
                        $('#gender-dummy').val(data.faculty_gender_title);
                        $('#gender').val(data.faculty_gender);
                        $('#blood-group-dummy').val(data.faculty_blood_group_title);
                        $('#blood-group').val(data.faculty_blood_group);
                        $('#aadhar-number').val(data.faculty_aadhar_number);
                        $('#religion-dummy').val(data.faculty_religion_title);
                        $('#religion').val(data.faculty_religion);
                        $('#caste-dummy').val(data.faculty_caste_title);
                        $('#caste').val(data.faculty_caste);
                        $('#community-dummy').val(data.faculty_community_title);
                        $('#community').val(data.faculty_community);
                        $('#nationality-dummy').val(data.faculty_nationality_title);
                        $('#nationality').val(data.faculty_nationality);
                        $('#marital-status-dummy').val(data.faculty_marital_status_title);
                        $('#marital-status').val(data.faculty_marital_status);




                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }

        const fetch_faculty_official_details = () => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_profile/json/fetch_faculty_official_details.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        $('#faculty-designation').val(data.designation);
                        $('#faculty-designation-dummy').val(data.designation_title);
                        $('#faculty-dept').val(data.dept_id);
                        $('#faculty-dept-dummy').val(data.department_title);
                        $('#faculty-salary').val(data.faculty_salary);
                        $('#joining-date').val(data.faculty_joining_date);

                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(jqXHR) {
                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                    showToast('error', message);
                }
            });
        }
        const fetch_faculty_contact_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_contact_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        $('#personal-mail-id').val(data.faculty_personal_mail_id);
                        $('#official-mail-id').val(data.faculty_official_mail_id);
                        $('#mobile-number').val(data.faculty_mobile_number);
                        $('#alt-mobile-number').val(data.faculty_alternative_contact_number);
                        $('#whatsapp-mobile-number').val(data.faculty_whatsapp_number);

                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }

            });
        }

        const fetch_faculty_address_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_address_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        $('#address-house-number').val(data.faculty_address_no);
                        $('#address-street').val(data.faculty_address_street);
                        $('#address-locality').val(data.faculty_address_locality);
                        $('#address-pincode').val(data.faculty_address_pincode);
                        $('#address-city').val(data.faculty_address_city);
                        $('#address-district').val(data.faculty_address_district);
                        $('#address-state').val(data.faculty_address_state);
                        $('#address-country').val(data.faculty_address_country);

                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }


        const load_update_profile_components = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'

            if (action == 'add' && route == 'faculty' && type == 'personal') {
                await PersonalProfileForm();
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').removeClass('active');
                $('.step.experiencestep').removeClass('active');
                $('.step.skillstep').removeClass('active');
                $('.step.documentuploadstep').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'education') {
                await EducationProfileForm();
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.experiencestep').removeClass('active');
                $('.step.skillstep').removeClass('active');
                $('.step.documentuploadstep').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'experience') {
                await ExperiencesProfileForm();
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.experiencestep').addClass('active');
                $('.step.skillstep').removeClass('active');                
                $('.step.documentuploadstep').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'skill') {
                await SkillProfileForm();
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.experiencestep').addClass('active');
                $('.step.skillstep').addClass('active');              
                $('.step.documentuploadstep').removeClass('active');
            } 
             else if (action == 'add' && route == 'faculty' && type == 'upload') {
                await UploadProfileForm();
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.experiencestep').addClass('active');
                $('.step.skillstep').addClass('active');
                $('.step.documentuploadstep').addClass('active');
            }
        }

        const load_personal_info_components = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'

            if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'personal') {
                await load_personal_profile_info_form();
                $('.tab-btn.personal').addClass('active');
                $('.tab-btn.contact').removeClass('active');
                $('.tab-btn.address').removeClass('active');
                $('.tab-btn.official').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'contact') {
                await load_contact_profile_info_form();
                $('.tab-btn.contact').addClass('active');
                $('.tab-btn.personal').removeClass('active');
                $('.tab-btn.address').removeClass('active');
                $('.tab-btn.official').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'address') {
                await load_address_profile_info_form();
                $('.tab-btn.address').addClass('active');
                $('.tab-btn.contact').removeClass('active');
                $('.tab-btn.personal').removeClass('active');
                $('.tab-btn.official').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'official') {
                await load_official_profile_info_form();
                $('.tab-btn.address').removeClass('active');
                $('.tab-btn.contact').removeClass('active');
                $('.tab-btn.personal').removeClass('active');
                $('.tab-btn.official').addClass('active');
            }
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
