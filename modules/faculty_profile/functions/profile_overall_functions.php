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
        const loadSidebar = () => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#sidebar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };



        const loadTopbar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/topbar.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#topbar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const loadBreadcrumbs = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/breadcrumbs.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#breadcrumbs').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };


        const load_faculty_student_personal_profile = (student_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/faculty_student_profile_view.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_id': student_id
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const load_personal_profile = (faculty_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/profile_view.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'faculty_id': faculty_id
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const load_overall_personal_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/overall_faculty_profile.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const load_overall_personal_faculty_student_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/overall_student_profile.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const load_update_personal_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/update_profile_info_faculty.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const load_faculty_profile_dashboard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/faculty_profile_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#dashboards').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const load_student_profile_dashboard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/student_profile_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#dashboards').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const fetch_bg_card_title = (routing_link) => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/ajax/fetch_bg_card_title.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'routing_link': routing_link
                    },
                    success: function(response) {
                        console.log(routing_link)
                        response = JSON.parse(response)
                        console.log(response)
                        const data = response.data;

                        $('#bg-card-title').text(data);
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }
        const callAction = (element = '') => {

            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'

            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search

            fetch_bg_card_title(routing_link);
            // Capitalize the first letter of the action and format the title
            let title = "";
            if (action) {
                if (action == 'edit') {
                    $('.bg-card-edit-button').hide();
                    $('.bg-card-filter').hide();
                    $('.bg-card-view-button').show();
                    $('.bg-card-add-button').show();
                    $('.full-width-hr').hide();
                } else if (action == 'view') {
                    $('.bg-card-edit-button').show();
                    $('.bg-card-view-button').hide();
                    $('.bg-card-add-button').show();
                    $('.full-width-hr').show();
                    $('.bg-card-filter').show();
                } else if (action == 'add') {
                    $('.full-width-hr').hide();
                    $('.bg-card-filter').hide();
                    $('.bg-card-add-button').hide();
                    $('.bg-card-edit-button').show();
                    $('.bg-card-view-button').show();
                }
            }


        }


        const faculty_overall_profile_table = (designation = 0, department = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/faculty_overall_profile_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'designation': designation,
                        'department': department
                    },
                    success: function(response) {
                        $('#overall-profile-table').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const student_overall_profile_table = (department = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/faculty_student_overall_profile_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'department': department
                    },
                    success: function(response) {
                        $('#overall-profile-table').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const Print_faculty_pdf = (designation, department) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/json/overall_profile_table_data.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'designation': designation,
                        'department': department,
                        'type': 2
                    },
                    xhrFields: {
                        responseType: 'blob' // Set the response type to blob
                    },
                    success: function(response) {
                        console.log(response);
                        // Create a link to download the PDF
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "Faculty Profile.pdf"; // Set the download filename
                        link.click(); // Trigger the download
                        resolve(); // Resolve the promise
                        showToast('success', "ðŸŽ‰âœ¨ " + link.download + " downloaded successfully! ðŸš€ Keep rocking, professor! ðŸ™Œ");


                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const Print_student_pdf = (year_of_study, section, department) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/json/overall__student_profile_table_data.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'year_of_study': year_of_study,
                        'section': section,
                        'department': department,
                        'type': 2
                    },
                    xhrFields: {
                        responseType: 'blob' // Set the response type to blob
                    },
                    success: function(response) {
                        console.log(response);
                        // Create a link to download the PDF
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "Faculty Profile.pdf"; // Set the download filename
                        link.click(); // Trigger the download
                        resolve(); // Resolve the promise
                        showToast('success', "ðŸŽ‰âœ¨ " + link.download + " downloaded successfully! ðŸš€ Keep rocking, professor! ðŸ™Œ");


                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };


        const load_faculty_overall_profile_table = (designation, department) => {
            $('#profileTable').DataTable().destroy()
            $('#profileTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_profile/json/overall_profile_table_data.php' ?>",
                    "type": "POST",
                    "data": {
                        "designation": designation,
                        "department": department,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "faculty_first_name"
                    },
                    {
                        "data": "dept_short_name"
                    },
                    {
                        "data": "designation"
                    },
                    {
                        "data": "status",
                        "orderable": false
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                },
            });
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        }


        const change_faculty_status = (faculty_id, faculty_status) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_change_status.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'faculty_id': faculty_id,
                        'faculty_status': faculty_status
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        showToast(response.status, response.message)
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },

                });
            });
        }

        const view_individual_faculty_profile = (faculty_id) => {
            const params = {
                action: 'view',
                route: 'faculty',
                type: 'overall',
                id: faculty_id
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&id=${params.id}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            load_personal_profile(faculty_id);
        }
        const view_individual_student_profile = (student_id) => {
            console.log("Student ID:", student_id);
            const params = {
                action: 'view',
                route: 'student',
                type: 'overall',
                id: student_id
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&id=${params.id}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            load_faculty_student_personal_profile(student_id);
        }

        const load_overall_student_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/faculty_student_overall_profile_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#pv').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }
        const fetch_all_individual_data_profile_faculty = (faculty_id) => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_all_individual_data_profile_faculty.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                data: {
                    'faculty_id': faculty_id
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const personal_data = response.data[0][0]['faculty_account_id'] ? response.data[0][0] : null;
                        const contact_data = response.data[1] ? response.data[1][0] : null;
                        const address_data = response.data[2] ? response.data[2][0] : null;
                        const sslc = response.data[3] ? response.data[3][0] : null;
                        const hsc = response.data[4] ? response.data[4][0] : null;
                        const degrees = response.data[5] || null;
                        const document = response.data[6] || null;
                        const experience = response.data[7] || null;
                        const skills = response.data[8] || null;
                        if (personal_data) {
                            $('#faculty-first-name').html(personal_data.faculty_first_name);
                            $('#faculty-middle-name').html(personal_data.faculty_middle_name);
                            $('#faculty-last-name').html(personal_data.faculty_last_name);
                            $('#faculty-initial').html(personal_data.faculty_initial);

                            // $('#faculty-first-name').html(personal_data.faculty_first_name);
                            // $('#faculty-middle-name').html(personal_data.faculty_middle_name);
                            //  $('#faculty-last-name').html(personal_data.faculty_last_name);
                            //  $('#faculty-initial').html(personal_data.faculty_initial);
                            $('#faculty-Fullname').html(personal_data.faculty_first_name + ' ' + personal_data.faculty_middle_name + ' ' + personal_data.faculty_last_name + ' ' + personal_data.faculty_initial)
                            $('#faculty-salutation').html(personal_data.faculty_salutation_title);
                            $('#faculty-dob').text(personal_data.faculty_dob);
                            $('#faculty-age').text(personal_data.faculty_age);
                            // $('#faculty-age').text(personal_data.faculty_age); // Add age calculation logic if needed
                            $('#faculty-gender').text(personal_data.faculty_gender_title);
                            $('#faculty-blood-group').text(personal_data.faculty_blood_group_title);
                            $('#faculty-aadhar-number').text(personal_data.faculty_aadhar_number);
                            $('#faculty-religion').text(personal_data.faculty_religion_title);
                            $('#faculty-caste').text(personal_data.faculty_caste_title);
                            $('#faculty-community').text(personal_data.faculty_community_title);
                            $('#faculty-nationality').text(personal_data.faculty_nationality_title);
                            $('#faculty-marital-status').text(personal_data.faculty_marital_status_title);
                        } else {
                            $("#profile-details-section").remove();
                        }
                        if (contact_data) {
                            $('#faculty-email-id').text(contact_data.faculty_personal_mail_id);
                            $('#faculty-official-email-id').text(contact_data.faculty_official_mail_id);
                            $('#faculty-mobile-number').text(contact_data.faculty_mobile_number);
                            $('#faculty-alernative-contact-number').text(contact_data.faculty_alternative_contact_number);
                            $('#faculty-whatsapp-number').text(contact_data.faculty_whatsapp_number);
                        } else {
                            $("#contact-details-section").remove();
                        }
                        if (address_data) {
                            $('#house-number').text(address_data.faculty_address_no);
                            $('#street').text(address_data.faculty_address_street);
                            $('#locality').text(address_data.faculty_address_locality);
                            $('#city').text(address_data.faculty_address_city);
                            $('#district').text(address_data.faculty_address_district);
                            $('#state').text(address_data.faculty_address_state);
                            $('#country').text(address_data.faculty_address_country);
                            $('#pincode').text(address_data.faculty_address_pincode);
                        } else {
                            $("#address-details-section").remove();
                        }
                        if (sslc) {
                            $('#school-name').text(sslc.sslc_institution_name);
                            $('#board-title').text(sslc.board_title);
                            $('#year-of-passing').text(sslc.sslc_passed_out_year);
                            $('#total-marks').text(sslc.sslc_total_marks); // Make sure `sslc_total_marks` exists in the data
                            $('#percentage').text(sslc.sslc_percentage + '%');
                        } else {
                            $("#sslc-details-section").remove();
                        }
                        if (hsc) {
                            $('#hsc-school-name').text(hsc.hsc_institution_name);
                            $('#hsc-board-title').text(hsc.board_title);
                            $('#hsc-year-of-passing').text(hsc.hsc_passed_out_year);
                            $('#hsc-total-marks').text(hsc.hsc_total_marks || 'N/A'); // Ensure hsc_total_marks exists, or display 'N/A'
                            $('#hsc-percentage').text(hsc.hsc_percentage + '%');
                            $('#hsc-cut-off-marks').text(hsc.hsc_cut_off_marks || 'N/A'); // Ensure hsc_cut_off_marks exists, or display 'N/A'
                        } else {
                            $("#hsc-details-section").remove();
                        }
                        if (degrees.degree_title != "") {
                            const degreesContainer = $("#degrees-container");
                            degreesContainer.empty(); // Clear any existing content

                            // Loop through each degree and create HTML for it
                            degrees.forEach(degree => {
                                const degreeRow = $(`
                                    <div class="row mt-3">
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Institution Name</div>
                                            <div class="value">${degree.faculty_edu_institution_name || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Degree</div>
                                            <div class="value">${degree.degree_title || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Specialization</div>
                                            <div class="value">${degree.specialization_title || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Year of Passing</div>
                                            <div class="value">${degree.faculty_edu_passed_out_year || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">CGPA</div>
                                            <div class="value">${degree.faculty_edu_cgpa || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Percentage</div>
                                            <div class="value">${degree.faculty_edu_percentage || 'N/A'}%</div>
                                        </div>
                                    </div>
                                `);

                                // Append the degree row to the degrees container
                                degreesContainer.append(degreeRow);

                                // Append a horizontal line separator after each degree
                                degreesContainer.append('<hr>');
                            });
                        } else {
                            $("#degrees-details-section").remove();
                        }

                        if (experience.faculty_exp_id != "") {
                            const experienceContainer = $("#experience-container");
                            experienceContainer.empty(); // Clear any existing content

                            // Loop through each experience and create HTML for it
                            experience.forEach(experiences => {
                                const fieldOfExperience = experiences.faculty_exp_field_of_experience == 1 ? "Industry" : "Institution";

                                const experienceRow = $(`
                                    <div class="row mt-3">
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Field Of Experience</div>
                                            <div class="value">${fieldOfExperience}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Name Of The ${fieldOfExperience}</div>
                                            <div class="value">${experiences.faculty_exp_industry_name || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Designation</div>
                                            <div class="value">${experiences.faculty_exp_designation || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Department</div>
                                            <div class="value">${experiences.faculty_exp_specialization || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Timespan Start Date</div>
                                            <div class="value">${experiences.faculty_exp_start_date || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Timespan End Date</div>
                                            <div class="value">${experiences.faculty_exp_end_date || 'N/A'}</div>
                                        </div>
                                    </div>
                                `);

                                // Append the experience row to the experience container
                                experienceContainer.append(experienceRow);

                                // Append a horizontal line separator after each experience entry
                                experienceContainer.append('<hr>');
                            });
                        } else {
                            $("#experience-details-section").remove();
                        }

                        if (skills[0].faculty_skill_id != "") {
                            // Get containers for each skill category
                            const coreExperienceContainer = $("#core-experience-container");
                            const softwareSkillsContainer = $("#software-skills-container");
                            const interestContainer = $("#interest-container");
                            const languagesKnownContainer = $("#languages-known-container");

                            // Clear existing skills (if any)
                            coreExperienceContainer.empty();
                            softwareSkillsContainer.empty();
                            interestContainer.empty();
                            languagesKnownContainer.empty();

                            // Loop through the skills array and append skills to the appropriate category
                            skills.forEach(skill => {
                                if (skill.faculty_skill_name) { // Only include skills with non-empty names
                                    let chip = `<span class="chip">${skill.faculty_skill_name}</span>`;
                                    switch (skill.faculty_skill_type) {
                                        case '1': // Core Experience
                                            coreExperienceContainer.append(chip);
                                            break;
                                        case '2': // Software Skills
                                            softwareSkillsContainer.append(chip);
                                            break;
                                        case '3': // Interest
                                            interestContainer.append(chip);
                                            break;
                                        case '4': // Languages Known
                                            languagesKnownContainer.append(chip);
                                            break;
                                        default:
                                            console.warn("Unknown skill type:", skill.faculty_skill_type);
                                    }
                                }
                            });
                        } else {
                            $("#skills-details-section").remove();
                        }


                        if (document) {
                            console.log(document);
                            const documentsContainer = $("#documents-container");

                            // Clear any existing content
                            documentsContainer.empty();

                            // Define icons for different file types
                            const icons = {
                                pdf: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/pdfs.svg' ?>",
                                doc: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                docx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                xls: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                xlsx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                default: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/unknown file.svg' ?>" // for unknown file types
                            };
                            const docPaths = {
                                1: "<?= GLOBAL_PATH . '/uploads/faculty_resumes/' ?>",
                                2: "<?= GLOBAL_PATH . '/uploads/faculty_sslc_certificate/' ?>",
                                3: "<?= GLOBAL_PATH . '/uploads/faculty_hsc_certificate/' ?>",
                                4: "<?= GLOBAL_PATH . '/uploads/faculty_highest_qualification_certificate/' ?>",
                                5: "<?= GLOBAL_PATH . '/uploads/faculty_experience_certificate/' ?>",
                                6: "<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>"
                            };

                            // Iterate through the documents array and dynamically create document cards
                            document.forEach(doc => {
                                console.log(doc);
                                if (doc.faculty_doc_type == 6) {
                                    $('#view-profile-pic').attr('src', '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + doc.faculty_doc_path)
                                }
                                const fileName = doc.faculty_doc_path.split('/').pop();
                                const fileExtension = fileName.split('.').pop().toLowerCase();

                                const basePath = docPaths[doc.faculty_doc_type] || "<?= GLOBAL_PATH . '/uploads/' ?>"; // Default path if type is not defined
                                const iconSrc = icons[fileExtension] || icons.default;

                                const documentRow = $(`
                                    <div class="profile-title mt-2">${fileName.split('-')[0]}</div>
                                    <div class="row mt-5 document-card">
                                        <div class="col col-1">
                                            <div class="icon">
                                                <img src="${iconSrc}" alt="Document Icon" width="50" height="50" />
                                            </div>
                                        </div>
                                        <div class="col col-9">
                                            <div class="document-details">
                                                <h3>Document Name: ${fileName.split('-')[0]}</h3>
                                                <p>Uploaded on: <span class="date-time">2024-11-01 10:00 AM</span></p>
                                            </div>
                                        </div>
                                        <div class="col col-2">
                                            <div class="download-option">
                                                
                                                <a href="${basePath}${doc.faculty_doc_path}" download>Download</a>
        
                                            </div>
                                        </div>
                                    </div>
                                `);

                                // Append the document row to the container
                                documentsContainer.append(documentRow);

                                // Optional: Add a horizontal line separator after each document row
                                documentsContainer.append('<hr>');
                            });


                        } else {
                            $("#document-details-section").remove();
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

        const fetch_all_individual_admission_data = (student_id) => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_all_individual_admission_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                data: {
                    'student_id': student_id
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {

                        // console.log(response.data);
                        const personal_data = response.data[0] ? response.data[0][0] : null;
                        const address_data = response.data[3] ? response.data[3][0] : null;
                        const parent_data = response.data[1] ? response.data[1][0] : null;
                        const contact_data = response.data[2] ? response.data[2][0] : null;
                        const sslc = response.data[4] ? response.data[4][0] : null;
                        const hsc = response.data[5] ? response.data[5][0] : null;
                        const degrees = response.data[6] || null;
                        const course = response.data[7][0] || null;
                        const document = response.data[8] || null;
                        // console.log(document);
                        // console.log(skills);
                        if (personal_data) {
                            $('#student-first-name').text(personal_data.student_first_name || '-');
                            $('#student-middle-name').text(personal_data.student_middle_name || '-');
                            $('#student-last-name').text(personal_data.student_last_name || '-');
                            $('#student-initial').text(personal_data.student_initial || '-');
                            $('#student-fullname').text(
                                `${personal_data.student_first_name || ''} ${personal_data.student_middle_name || ''} ${personal_data.student_last_name || ''} ${personal_data.student_initial || ''}`.trim()
                            );

                            $('#student-dob').text(personal_data.student_dob || '-');
                            $('#student-age').text(personal_data.student_age);
                            $('#student-gender').text(personal_data.student_gender_title || '-');
                            $('#student-blood-group').text(personal_data.student_blood_group_title || '-');

                            $('#student-official-email').text(personal_data.student_official_email || '-');
                        } else {
                            console.error('No personal data available to display.');
                        }
                        if (address_data) {
                            // Permanent Address
                            $('#permanent-door-no').text(address_data.student_address_no || '-');
                            $('#permanent-street').text(address_data.student_address_street || '-');
                            $('#permanent-area').text(address_data.student_address_locality || '-');
                            $('#permanent-district').text(address_data.student_address_district || '-');
                            $('#permanent-state').text(address_data.student_address_state || '-');
                            $('#permanent-pincode').text(address_data.student_address_pincode || '-');

                            // // Residential Address (assuming residential data is the same as permanent data in this case)
                            // $('#residential-door-no').text(address_data.student_address_no || '-');
                            // $('#residential-street').text(address_data.student_address_street || '-');
                            // $('#residential-area').text(address_data.student_address_locality || '-');
                            // $('#residential-district').text(address_data.student_address_district || '-');
                            // $('#residential-state').text(address_data.student_address_state || '-');
                            // $('#residential-pincode').text(address_data.student_address_pincode || '-');
                        } else {
                            console.error('No address data available to display.');
                        }
                        if (contact_data) {
                            // Phone Number (assuming mobile number is the same as phone number)
                            $('#contact-phone-number').text(contact_data.student_mobile_number || '-');
                            $('#contact-whatsapp-number').text(contact_data.student_whatsapp_number || '-');
                            $('#contact-alternativenumber-number').text(contact_data.student_alternative_contact_number || '-');
                            $('#student-email').text(contact_data.student_email_id || '-');
                            // Admission Type (If you have this data, you can update it accordingly)
                            $('#contact-admission-type').text(contact_data.student_admission_type || '-');

                            // Date of Admission (Assuming you have this data available)
                            $('#contact-date-of-admission').text(contact_data.student_date_of_admission || '-');

                            // Religion
                            $('#contact-religion').text(contact_data.student_religion || '-');

                            // Community
                            $('#contact-community').text(contact_data.student_community || '-');

                            // Aadhar Number
                            $('#contact-aadhar-number').text(contact_data.student_aadhar_number || '-');

                            // PAN Number (If available in the contact data)
                            $('#contact-pan-number').text(contact_data.student_pan_number || '-');
                        } else {
                            console.error('No contact data available to display.');
                        }
                        if (parent_data) {
                            // Father's Information
                            $('#father-name').text(parent_data.student_father_name || 'N/A');
                            // $('#father-mobile').text(parent_data.student_father_mobile || 'N/A'); // You may need to add mobile number data if available
                            $('#father-occupation').text(parent_data.student_father_occupation || 'N/A');

                            // Mother's Information
                            $('#mother-name').text(parent_data.student_mother_name || 'N/A');
                            // $('#mother-mobile').text(parent_data.student_mother_mobile || 'N/A'); // Same as above, for mobile number
                            $('#mother-occupation').text(parent_data.student_mother_occupation || 'N/A');

                            // Guardian's Information
                            $('#guardian-name').text(parent_data.student_guardian_name || 'N/A');
                            $('#guardian-occupation').text(parent_data.student_guardian_occupation || 'N/A');
                            // $('#guardian-mobile').text(parent_data.student_guardian_mobile || 'N/A'); // If guardian's mobile number is available, update it
                        } else {
                            console.error('No parent data available to display.');
                        }
                        // Check if the SSL data is available and populate the fields
                        if (sslc) {
                            $('#sslc-school-name').text(sslc.sslc_institution_name || 'N/A');
                            $('#sslc-board').text(sslc.board_title || 'N/A');
                            $('#sslc-passing-year').text(sslc.sslc_passed_out_year || 'N/A');
                            $('#sslc-total-marks').text(sslc.sslc_mark || 'N/A');
                            $('#sslc-percentage').text(sslc.sslc_percentage || 'N/A');
                        } else {
                            console.log("No SSLC data available.");
                        }

                        // Check if the HSC data is available and populate the fields
                        if (hsc) {
                            $('#hsc-school-name').text(hsc.hsc_institution_name || 'N/A');
                            $('#hsc-board').text(hsc.board_title || 'N/A');
                            $('#hsc-passing-year').text(hsc.hsc_passed_out_year || 'N/A');
                            $('#hsc-total-marks').text(hsc.hsc_mark || 'N/A');
                            $('#hsc-percentage').text(hsc.hsc_percentage || 'N/A');
                            $('#specialization-title').text(hsc.specialization_title || 'N/A'); // Assuming 'specialization_title' is the cut-off marks
                        } else {
                            console.log("No HSC data available.");
                        }
                        if (degrees) {
                            const degreesContainer = $("#degrees-container");
                            degreesContainer.empty(); // Clear any existing content

                            // Loop through each degree and create HTML for it
                            degrees.forEach(degree => {
                                const degreeRow = $(`
                                    <div class="row mt-3">
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Institution Name</div>
                                            <div class="value">${degree.student_edu_institution_name || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Degree</div>
                                            <div class="value">${degree.degree_title || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Specialization</div>
                                            <div class="value">${degree.specialization_title || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Year of Passing</div>
                                            <div class="value">${degree.student_edu_passed_out_year || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">CGPA</div>
                                            <div class="value">${degree.student_edu_cgpa || 'N/A'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Percentage</div>
                                            <div class="value">${degree.student_edu_percentage || 'N/A'}%</div>
                                        </div>
                                    </div>
                                `);

                                // Append the degree row to the degrees container
                                degreesContainer.append(degreeRow);

                                // Append a horizontal line separator after each degree
                                degreesContainer.append('<hr>');
                            });
                        }
                        if (course) {
                            let admissionDate = new Date(course.student_admission_date);


                            // Format the date as YYYY/MM/DD
                            let formattedDate = admissionDate.getFullYear() + '/' +
                                (admissionDate.getMonth() + 1).toString().padStart(2, '0') + '/' +
                                admissionDate.getDate().toString().padStart(2, '0');
                            $('#course_pref1').text(course.dept1_title);
                            $('#course_pref2').text(course.dept2_title);
                            $('#course_pref3').text(course.dept3_title);
                            $('#contact-date-of-admission').text(formattedDate);

                            // Set the faculty reference
                            $('#faculty_reference').text(course.general_title + course.faculty_first_name + ' ' + course.faculty_last_name);

                            // Set the "How Did You Hear About Us"
                            const knowAboutUs = [{
                                    title: "Friends or Family",
                                    value: 1
                                },
                                {
                                    title: "Social Media",
                                    value: 2
                                },
                                {
                                    title: "Website",
                                    value: 3
                                },
                                {
                                    title: "Advertisement",
                                    value: 4
                                },
                                {
                                    title: "Events or Workshops",
                                    value: 5
                                },
                                {
                                    title: "Other",
                                    value: 6
                                }
                            ];
                            $('#know_about_us').text(knowAboutUs.find(item => item.value == course.student_admission_know_about_us)?.title || 'N/A');

                            // Set the admission type
                            const admissionType = [{
                                    title: "Centac",
                                    value: 1
                                },
                                {
                                    title: "Management",
                                    value: 2
                                }
                            ];
                            $('#admission_type').text(admissionType.find(item => item.value == course.student_admission_type)?.title || 'N/A');

                            // Set the admission method (Lateral Entry or New Admission)
                            const lateralEntry = [{
                                    title: "No",
                                    value: 1
                                },
                                {
                                    title: "Yes",
                                    value: 2
                                }
                            ];
                            $('#admission_method').text(lateralEntry.find(item => item.value == course.lateral_entry_year_of_study)?.title || 'N/A');

                            // Set the hostel and transport
                            const residency = [{
                                    title: "YES",
                                    value: 1
                                },
                                {
                                    title: "NO",
                                    value: 0
                                }
                            ];
                            $('#hostel').text(residency.find(item => item.value == course.student_hostel)?.title || 'N/A');

                            const transport = [{
                                    title: "Yes",
                                    value: 1
                                },
                                {
                                    title: "No",
                                    value: 0
                                }
                            ];
                            $('#transport').text(transport.find(item => item.value == course.student_transport)?.title || 'N/A');

                            // Set admission status



                        } else {
                            console.error('No personal data available to display.');
                        }


                        if (document) {

                            const documentsContainer = $("#documents-container");

                            // Clear any existing content
                            documentsContainer.empty();

                            // Define icons for different file types
                            const icons = {
                                pdf: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/pdfs.svg' ?>",
                                doc: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                docx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                xls: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                xlsx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                default: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/unknown file.svg' ?>" // for unknown file types
                            };
                            const docPaths = {
                                1: "<?= GLOBAL_PATH . '/uploads/student_sslc/' ?>",
                                2: "<?= GLOBAL_PATH . '/uploads/student_hsc_certificate/' ?>",
                                3: "<?= GLOBAL_PATH . '/uploads/student_highest_qualification/' ?>",
                                4: "<?= GLOBAL_PATH . '/uploads/student_transfer_certificate/' ?>",
                                5: "<?= GLOBAL_PATH . '/uploads/student_permanent_integrated_certificate/' ?>",
                                6: "<?= GLOBAL_PATH . '/uploads/student_community_certificate/' ?>",
                                7: "<?= GLOBAL_PATH . '/uploads/student_residence_certificate/' ?>",
                                8: "<?= GLOBAL_PATH . '/uploads/student_profile_pic/' ?>"
                            };

                            // Iterate through the documents array and dynamically create document cards
                            document.forEach(doc => {

                                if (doc.student_doc_type == 8) {
                                    $('#view-profile-pic').attr('src', '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + doc.student_doc_path)
                                }
                                const fileName = doc.student_doc_path.split('/').pop();
                                const fileExtension = fileName.split('.').pop().toLowerCase();

                                const basePath = docPaths[doc.student_doc_type] || "<?= GLOBAL_PATH . '/uploads/' ?>"; // Default path if type is not defined
                                const iconSrc = icons[fileExtension] || icons.default;

                                const documentRow = $(`
        <div class="profile-title mt-2">${fileName.split('-')[0]}</div>
        <div class="row mt-5 document-card">
            <div class="col col-1">
                <div class="icon">
                    <img src="${iconSrc}" alt="Document Icon" width="50" height="50" />
                </div>
            </div>
            <div class="col col-9">
                <div class="document-details">
                    <h3>Document Name: ${fileName.split('-')[0]}</h3>
                    <p>Uploaded on: <span class="date-time">2024-11-01 10:00 AM</span></p>
                </div>
            </div>
            <div class="col col-2">
                <div class="download-option">
                    
                    <a href="${basePath}${doc.student_doc_path}" download>Download</a>

                </div>
            </div>
        </div>
    `);

                                // Append the document row to the container
                                documentsContainer.append(documentRow);

                                // Optional: Add a horizontal line separator after each document row
                                documentsContainer.append('<hr>');
                            });


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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
