<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
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
        const logged_role_id = <?= $logged_role_id ?>;
        var faculty_name_list = [];
        var student_name_list = [];
        var role_list = [{
                title: 'Head',
                value: 1
            },
            {
                title: 'Co Ordinator',
                value: 2
            },
            {
                title: 'Associate Co Ordinator',
                value: 3
            },
            {
                title: 'Member',
                value: 4
            },
        ];
        var class_data = [];
        var faculty_details = [];
        var mentor_data = [];
        var dept_id = <?= json_encode($secondary_roles) ?>.includes(<?= $logged_role_id ?>) ? <?= $logged_dept_id ?> : 0;


        const load_filter = (route, type) => {
            if (route == 'faculty') {
                $("#committee-dept-filter").toggle(dept_id == 0);

                if (type == 'authorities') {
                    $("#committee-role-filter").hide();
                    $("#committee-dept-filter").hide();
                } else if (type == 'committees') {
                    $("#committee-role-filter").show();
                } else if (type == 'class_advisors' || type == 'mentors') {
                    $("#committee-role-filter").hide();
                }
            } else if (route == 'student') {
                $("#committee-dept-filter").toggle(dept_id == 0);

                if (type == 'representatives') {
                    $("#committee-role-filter").hide();
                } else if (type == 'committees') {
                    $("#committee-role-filter").show();
                } else if (type == 'class_advisors' || type == 'mentors') {
                    $("#committee-role-filter").hide();
                }
            }
        };

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

        const loadFooter = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/footer.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#footer').html(response);
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

        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#bg-card').html(response);
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




        const load_faculty_dept_reset_form_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_dept_reset_form_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#mentor-faculty-popup').html(response);

                        // Close the popup
                        $('.popup-close-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        // Close the popup
                        $('.mentor-rest-cancel-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }
        const load_faculty_student_mentor = (faculty_id, dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/faculty_student_mentor_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    data: {
                        'faculty_id': faculty_id,
                        'dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const class_data = response.class_data
                            const mentor_data = response.mentor_data

                            if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                                generate_mentor_accordian(class_data, mentor_data);
                            } else {
                                generate_individual_mentor_accordian(class_data, mentor_data);
                            }

                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }

        const load_mentor_faculty_reset_confirmation_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_mentor_reset_confirmation_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#mentor-faculty-popup').html(response);

                        // Close the popup
                        $('.popup-close-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        // Close the popup
                        $('.mentor-rest-cancel-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }

        const load_mentor_faculty_swap_popup = () => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_mentor_swap_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#mentor-faculty-popup').html(response);

                        // Close the popup
                        $('.popup-close-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        // Close the popup
                        $('.mentor-rest-cancel-btn').on('click', function() {
                            $('#mentor-faculty-popup').html("");
                        });
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }

        const load_mentor_edit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_dept_mentor_edit.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#role-edit-section').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }

        const roleView = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/view_roles_responsibilities.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#faculty-role-view').html(response);
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

        const roleStudentView = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_student_view_roles_responsibilities.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#faculty-role-view').html(response);
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
        const roleEdit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/edit_roles_responsibilities.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#faculty-role-edit').html(response);
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

        const roleStudentEdit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/components/faculty_student_edit_roles_responsibilities.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#faculty-role-edit').html(response);
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




        const fetch_faculty_name_list = (element, dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_faculty_name_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'faculty_dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        faculty_name_list = response.data;
                        const suggestions = element.siblings(".autocomplete-suggestions")
                        const value = element.siblings(".faculty-id")
                        const faculty_names_chips = getChipsValues($('#faculty-mentor-list-chips'))
                        faculty_name_list = faculty_name_list.filter(faculty => !faculty_names_chips.includes(faculty.title));

                        showSuggestions(faculty_name_list, suggestions, value, element);
                        resolve(faculty_name_list);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_student_name_list = (element, dept_id, year_of_study_id, section_id, group_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_student_name_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'faculty_dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'group_id': group_id

                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            student_name_list = response.data;
                            const suggestions = element.siblings(".autocomplete-suggestions")
                            const value = element.siblings(".student-id")

                            showSuggestions(student_name_list, suggestions, value, element);
                            resolve(student_name_list);
                        } else {
                            showToast(response.status, response.message);
                            reject(response);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_commitee_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_commitee_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const commitee_data = response.commitee_data;
                            const commitee_faculty_data = response.commitee_faculty_data;

                            populateCommitteeForm(commitee_data, commitee_faculty_data);
                            resolve(response);
                        } else {
                            showToast(response.status, response.message);
                            reject(response);
                        }

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_student_commitee_list = (action, dept_id, role_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_student_commitee_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id,
                        'role_id': role_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const commitee_data = response.commitee_data || [];
                            const commitee_student_data = response.commitee_student_data || [];
                            if (action == 'edit') {
                                populateStudentCommitteeForm(commitee_data, commitee_student_data);
                            } else if (action == 'view') {
                                populateStudentRolesData(commitee_student_data)

                            }
                            resolve(response);
                        } else {
                            showToast(response.status, response.message);
                            reject(response);
                        }

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_view_individual_roles = (role_id, faculty_id, dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_view_individual_roles.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    data: {
                        'role_id': role_id,
                        'faculty_id': faculty_id,
                        'dept_id': dept_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.roles_data;
                            if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                                populateRolesData(data);
                            } else {
                                populateIndividualRolesData(data);
                            }
                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const populate_edit_authorities_data = (data) => {
            $('#role-edit-section').empty()
            $('#role-edit-section').append(`
                    <form id="dept-authorities-roles-form" method="POST">
                        <div id="authorities-edit"></div>
                        <div class="text-center">
                            <button type="submit" class="primary text-center full-width">SUBMIT</button>
                        </div>
                    </form>`);
            data.forEach(item => {

                // If values are null, set them to 0
                const faculty_id = item.faculty_id || 0;
                const faculty_authorities_group_id = item.faculty_authorities_group_id || 0;
                const faculty_authorities_id = item.faculty_authorities_id || 0;
                const faculty_dept_id = item.dept_id || 0;
                const faculty_dept_title = item.dept_title || '';
                const full_name = item.full_name || "";
                const designation = item.designation || "";

                var section_title = ""; // Initialize an empty string for section_title

                switch (faculty_authorities_group_id) {
                    case 1:
                        section_title = "Principal";
                        break;
                    case 2:
                        section_title = "Vice Principal";
                        break;
                    case 3:
                        section_title = "Dean - Academics";
                        break;
                    case 4:
                        section_title = "Head Of the Department";
                        break;
                    case 5:
                        section_title = "Exam Cell Head";
                        break;
                    case 6:
                        section_title = "Admission Cell Head";
                        break;
                    case 7:
                        section_title = "Placement Cell Head";
                        break;
                    default:
                        section_title = "Unknown Role"; // Default value if no match is found
                        break;
                }


                // Create the HTML structure dynamically
                const html = `
                    <div class="row">
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6 pt-6">
                            <div class="section-header-title text-left">${section_title}</div>
                        </div>
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                            <div class="section-header-title text-right">${faculty_dept_title}</div>
                        </div>
                            <div class="input-container autocomplete-container">
                                <input type="text" class="auto faculty-authorities-name-dummy autocomplete-input" placeholder=" " value="${full_name}">
                                <label class="input-label">Select The Faculty Name</label>
                                <input type="hidden" name="faculty_id[]" class="faculty-id" value="${faculty_id}">
                                <input type="hidden" name="faculty_authorities_group_id[]" class="faculty-authorities-group-id" value="${faculty_authorities_group_id}">
                                <input type="hidden" name="faculty_authorities_id[]" class="faculty-authorities-id" value="${faculty_authorities_id}">
                                <input type="hidden" name="faculty_dept_id[]" class="faculty-dept-id" value="${faculty_dept_id}">
                                <span class="autocomplete-arrow">&#8964;</span>
                                <div class="autocomplete-suggestions"></div>
                            </div>
                    </div>
                `;

                // Append the generated HTML to the #authorities-edit container
                $('#authorities-edit').append(html);


            });
            $('#role-edit-section').find('.faculty-authorities-name-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_name_list(element, 0);
            });



            $('#role-edit-section').find('.faculty-authorities-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });

            $('#dept-authorities-roles-form').submit(function(e) {
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/faculty_authorities_roles_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: formData,
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        beforeSend: function() {
                            showComponentLoading(2)
                        },
                        success: function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);

                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        },
                        complete: function() {
                            hideComponentLoading(); // Hide the loading
                        }
                    });
                });
            });

        }


        const generate_mentor_faculty = (faculty_details) => {
            $('#role-view-section').html(`
                <div class="row">
                    <div class="col col-5 col-lg-5 col-md-12 col-sm-12 col-xs-12 mentor-faculty-card">
                    </div>
                    <div class="col col-7 col-lg-7 col-md-12 col-sm-12 col-xs-12 mentor-assignment-panel">

                    </div>

                </div>
            `)

            $('.mentor-faculty-card').html(`     
               <div class="main-content-card m-6 mentor-faculty-list"></div>
            `)
            // Clear or target the container to append the generated content
            const targetContainer = $('.mentor-faculty-list'); // Adjust this selector as needed
            targetContainer.empty();

            $('.mentor-assignment-panel').html(`
                <div class="main-content-card action-box" id="faculty-mentor-student-list">
                    <div class="action-title">Mentor Assignment Panel</div>
                    <div class="mentor-assignment">
                        <p class="action-text">
                            Select a faculty member to view their list of mentored students.
                        </p>
                        <div class="action-hint">
                           *Once a student under a great mentor, always a student prepared for greatness. Carry the lessons well, future leaders of tomorrow.*
                        </div>
                    </div>
                </div>
            `)
            if (faculty_details && faculty_details.length > 0) {
                faculty_details.forEach(faculty => {
                    // Extract faculty data with defaults for missing fields
                    const fullName = faculty.full_name || "Unknown Name";
                    const faculty_id = faculty.faculty_id || 0;
                    const dept_id = faculty.dept_id || 0;
                    const designation = faculty.designation || "N/A";
                    const profilePic = faculty.profile_pic ?
                        '<?= GLOBAL_PATH . "/uploads/faculty_profile_pic/" ?>' + faculty.profile_pic :
                        '<?= GLOBAL_PATH . "/images/profile pic placeholder.png" ?>';

                    // Create HTML structure
                    const facultyCard = $(`
                    <div class="row">
                        <div class="popup-card m-6 individual-faculty-list" data-faculty-id="${faculty_id}" data-dept-id="${dept_id}">
                            <div class="row align-items-center">
                                <div class="col col-3 col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <div class="profile-pic-avatar" id="profile-pic-avatar">
                                        <img src="${profilePic}" class="profile-pic-avatar-img" alt="${fullName}">
                                    </div>
                                </div>
                                <div class="col col-9 col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                    <h5 class="text-color">${fullName}</h5>
                                    <p class="text-light">${designation}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                    // Append to the target container
                    targetContainer.append(facultyCard);

                    // Add click event listener
                    $('.individual-faculty-list').on('click', async function() {
                        // Remove 'active' class from all other .individual-faculty-list elements
                        $('.individual-faculty-list').removeClass('active');

                        // Add 'active' class to the clicked element
                        $(this).addClass('active');

                        // Get faculty_id and handle accordingly
                        const faculty_id = $(this).data('faculty-id');
                        const dept_id = $(this).data('dept-id');

                        // Call the accordion generation function
                        await load_faculty_student_mentor(faculty_id, dept_id);
                    });
                });
            } else {
                $('#role-view-section').html(`
                <div class="main-content-card action-box" id="faculty-mentor-student-list">
                    <div class="action-title">Mentor Assignment Panel</div>
                    <div class="mentor-assignment">
                        <p class="action-text">
                            No mentor staff has been assigned to the students yet. Rest assured, assignments will be made soon to ensure every student has the guidance they need.
                        </p>
                        <div class="action-hint">
                           *A guide may not yet have arrived, but the stars always align for the journey to begin. Patience and perseverance lead the way to greatness.*
                        </div>
                    </div>
                </div>
            `)
            }
        }


        const generate_mentor_accordian = (class_data = [], mentor_data = []) => {
            if (!class_data || !mentor_data) {
                console.error("Invalid data provided");
                return;
            }

            // Group mentor data by year_of_study_id and section_id
            const groupedData = {};
            mentor_data.forEach(student => {
                const key = `${student.year_of_study_id}-${student.section_id}`;
                if (!groupedData[key]) {
                    groupedData[key] = [];
                }
                groupedData[key].push(student);
            });

            // Create the accordion structure
            const accordion = $('<div class="accordion"></div>');

            class_data.forEach(classItem => {
                const key = `${classItem.year_of_study_id}-${classItem.section_id}`;
                const sectionData = groupedData[key];

                // Check if there are students in the section
                if (sectionData && sectionData.length > 0) {
                    // Accordion item
                    const accordionItem = $(`
                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h5>${classItem.year_of_study_title} - Year ${classItem.section_title} - Section</h5>
                                <span class="accordion-icon">+</span>
                            </div>
                        </div>
                    `);

                    // Accordion content
                    const accordionContent = $('<div class="accordion-content"></div>');
                    const listContainer = $('<div class="list-container"></div>');

                    sectionData.forEach(student => {
                        const profilePic = student.profile_pic ?
                            '<?= GLOBAL_PATH . "/uploads/student_profile_pic/" ?>' + student.profile_pic :
                            '<?= GLOBAL_PATH . "/images/profile pic placeholder.png" ?>';

                        const listItem = $(`
                            <div class="list-item">
                                <div class="avatar">
                                    <img src="${profilePic}" class="avatar-img">
                                </div>
                                <span class="list-title">${student.full_name}</span>
                                <span class="list-badge">Reg: ${student.student_reg_number || "N/A"}</span>
                            </div>
                        `);
                        listContainer.append(listItem);
                    });

                    accordionContent.append(listContainer);
                    accordionItem.append(accordionContent);
                    accordion.append(accordionItem);
                }
            });

            // Append to the target container (adjust selector as needed)
            $('.mentor-assignment').html(accordion);
        };

        const generate_individual_mentor_accordian = (class_data = [], mentor_data = []) => {
            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="faculty-mentor-student-list">
                    <div class="action-title">Mentor Assignment Panel</div>
                    <div class="mentor-assignment">
                        <p class="action-text">
                            No students have been assigned under your mentorship yet. We will notify you once mentees are allocated.
                        </p>
                        <div class="action-hint">
                           *The bond between a mentor and mentee is like the bond of Eywa with the Na'vi. When it forms, it is strong, unbreakable, and life-giving.*
                        </div>
                    </div>
                </div>
            `)


            // Group mentor data by year_of_study_id and section_id
            const groupedData = {};
            mentor_data.forEach(student => {
                const key = `${student.year_of_study_id}-${student.section_id}`;
                if (!groupedData[key]) {
                    groupedData[key] = [];
                }
                groupedData[key].push(student);
            });




            if (class_data && class_data.length > 0) {
                // Create the accordion structure
                const accordion = $('<div class="accordion p-6"></div>');
                class_data.forEach(classItem => {
                    const key = `${classItem.year_of_study_id}-${classItem.section_id}`;
                    const sectionData = groupedData[key];

                    // Check if there are students in the section
                    if (sectionData && sectionData.length > 0) {
                        // Accordion item
                        const accordionItem = $(`
                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h5>${classItem.year_of_study_title} - Year ${classItem.section_title} - Section</h5>
                                <span class="accordion-icon">+</span>
                            </div>
                        </div>
                    `);

                        // Accordion content
                        const accordionContent = $('<div class="accordion-content"></div>');
                        const listContainer = $('<div class="list-container"></div>');

                        sectionData.forEach(student => {
                            const profilePic = student.profile_pic ?
                                '<?= GLOBAL_PATH . "/uploads/student_profile_pic/" ?>' + student.profile_pic :
                                '<?= GLOBAL_PATH . "/images/profile pic placeholder.png" ?>';

                            const listItem = $(`
                            <div class="list-item">
                                <div class="avatar">
                                    <img src="${profilePic}" class="avatar-img">
                                </div>
                                <span class="list-title">${student.full_name}</span>
                                <span class="list-badge">Reg: ${student.student_reg_number || "N/A"}</span>
                            </div>
                        `);
                            listContainer.append(listItem);
                        });

                        accordionContent.append(listContainer);
                        accordionItem.append(accordionContent);
                        accordion.append(accordionItem);
                    }
                });
                // Append to the target container (adjust selector as needed)
                $('.mentor-assignment').html(accordion);
            }

        };

        const populate_view_authorities_data = (data = []) => {

            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="college-authorities-list">
                    <div class="action-title">College Authorities Panel</div>
                    <div class="authorities-assignment">
                        <p class="action-text">
                            No authorities are available at the moment.
                        </p>
                        <div class="action-hint">
                           *Every great story has its heroes, and every hero finds their stage. Stay tuned for the next act!*
                        </div>
                    </div>
                </div>
            `);


            if (data && data.length > 0) {
                $('.authorities-assignment').html(`<div class="row" id="authorities-view-container"></div>`)
                data.forEach(item => {
                    // If values are null, set them to 0
                    const faculty_id = item.faculty_id || 0;
                    const faculty_authorities_group_id = item.faculty_authorities_group_id || 0;
                    const faculty_authorities_id = item.faculty_authorities_id || 0;
                    const faculty_dept_id = item.dept_id || 0;
                    const faculty_dept_title = item.dept_title || '';
                    const full_name = item.full_name || "Not Assigned";
                    const designation = item.designation || "";
                    const profile_pic_path = item.profile_pic_path || "";
                    const authority_title = item.authority_title || "";
                    var section_title = ""; // Initialize an empty string for section_title



                    // Create the HTML structure dynamically
                    const html = `
                        <div class="col col-3 col-lg-4 col-md-6 col-sm-6 col-xs-12">         
                            <div class="card full-width">
                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="section-header-title text-left">${authority_title}</div>
                                </div>
                                <div class="auth-img text-center">
                                    
                                        <img src="${profile_pic_path ? '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + profile_pic_path : '<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>'}" alt=""> 
                                </div>
                                <div class="details text-center">
                                    <h3 class="text-dark  value">${full_name}</h3>
                                    <p class="text-light">${designation}</p>
                                    <!-- Only show department_name when faculty_official_group_id == 4 -->
                                    ${faculty_authorities_group_id == 4 ? `<p class="text-light">${faculty_dept_title}</p>` : ''}

                                </div>
                            </div>
                        </div>
                    `;

                    // Append the generated HTML to the #authorities-edit container
                    $('#authorities-view-container').append(html);
                });
            }
        }

        const populate_individual_view_authorities_data = (data = []) => {
            $('#role-view-section').empty()

            $('#role-view-section').html(`
                    <div class="main-content-card action-box" id="college-authorities-list">
                        <div class="action-title">Your Assigned Roles</div>
                        <div class="authorities-assignment">
                            <p class="action-text">
                                "Looks like you’re still waiting for your superhero role to be assigned. Don't worry, we’ll give you a cape when the time comes!"
                            </p>
                            <div class="action-hint">
                               *Don’t worry, your role is like the One Ring — it’ll find you when the time is right.*
                            </div>
                        </div>
                    </div>

            `);

            if (data && data.length > 0) {
                $('.authorities-assignment').html(`<div class="row" id="authorities-view-container"></div>`)
                data.forEach(item => {
                    const faculty_dept_title = item.dept_title || '';
                    const effective_from = item.effective_from || "";
                    const effective_to = item.effective_to || "Till Date";
                    const authority_title = item.authority_title || "";
                    var section_title = ""; // Initialize an empty string for section_title



                    // Create the HTML structure dynamically
                    const html = `
                    <div class="card full-width">
                        <div class="row">
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                                <div class="section-header-title text-left ">${authority_title}</div>
                            </div>
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="section-header-title text-right ">${faculty_dept_title}</div>
                            </div>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective From</div>
                                <div class="value">${effective_from}</div>
                            </div>
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective To</div>
                                <div class="value">${effective_to}</div>
                            </div>
                        </div>
                    </div>
                `;

                    // Append the generated HTML to the #authorities-edit container
                    $('#authorities-view-container').append(html);
                });
            }
        }
        const fetch_dept_mentor_details = (dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_dept_mentor_details.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            var data = []
                            data = response.faculty_data
                            generate_mentor_faculty(data);
                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }
        const fetch_faculty_authorities = (action) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_faculty_authorities.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'faculty_id': <?= $logged_user_id ?>
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.roles_data;
                            if (action == 'edit') {
                                populate_edit_authorities_data(data);
                            } else if (action == 'view') {
                                if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                                    populate_view_authorities_data(data);
                                } else {
                                    populate_individual_view_authorities_data(data);
                                }

                            }

                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }

        const fetch_faculty_student_representatives = (action, dept_id, year_of_study_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_faculty_student_representatives.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            if (action == 'edit') {
                                populateStudentRepresentativeEditData(data)
                            } else if (action == 'view') {
                                populateStudentRepresentativeViewData(data)

                            }

                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }


        const fetch_class_advisors = (dept_id, year_of_study_id, action) => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/json/fetch_class_advisors.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id
                    },
                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            var data = [];
                            data = response.roles_data;
                            if (action == 'view') {
                                if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                                    populateClassAdvisorsData(data);
                                } else {
                                    populateIndividualClassAdvisorsData(data);
                                }
                            } else if (action == 'edit') {

                                populateClassAdvisorsEditData(data);
                            }
                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const populateClassAdvisorsData = (data = []) => {

            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="class-advisor-status">
                    <div class="action-title">Class Advisor Assignments Panel</div>
                    <div class="advisor-assignment-status">
                        <p class="action-text">
                            "If your department or class is not yet listed, rest assured that assignments are being finalized and will be updated shortly."
                        </p>
                        <div class="action-hint">
                            *Be patient — just like the gang waits for their next adventure, your role is coming soon.*
                        </div>
                    </div>
                </div>
            `);

            if (data && data.length > 0) {
                const container = $('.advisor-assignment-status');
                container.html(`<div class="row" id="class-advisors-view"></div>`);

                const class_advisors_view = $('#class-advisors-view');
                data.forEach(item => {
                    var full_name = "Not Assigned";

                    if (item.faculty_full_name != '   ') { // This checks for null, undefined, or empty string
                        full_name = (item.salutation ? item.salutation + ' ' : '') + item.faculty_full_name;
                    }

                    let roleElement = `
                    <div class="col col-2 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="card full-width">
                        <div class="row">
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                <div class="section-header-title text-left">${item.year_of_study_title} - Year </div>
                            </div>
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                <div class="section-header-title text-right">${item.section_title} - Section</div>
                            </div>
                        </div>
                            <div class="auth-img text-center">
                                <!-- Conditionally set the image source -->
                                <img src="${item.profile_pic ? '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + item.profile_pic : '<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>'}" alt="">
                            </div>
                            <div class="details text-center">
                                <h3 class="text-dark value">${full_name}</h3>
                                <p class="text-light">${item.designation}</p>
                                <p class="text-light">${item.dept_title}</p>
                                <p class="text-light">Batch : ${item.academic_batch_title}</p>
                                
                            </div>
                        </div>
                    </div>
                `;

                    class_advisors_view.append(roleElement)
                });
            }
        }

        const populateStudentRepresentativeViewData = (data = []) => {

            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="student-representative-status">
                    <div class="action-title">Student's Representative Panel</div>
                    <div class="student-rep-assignment-status">
                        <p class="action-text">
                            "The list of student representatives for your department and class is being carefully reviewed. Updates will be available soon."
                        </p>
                        <div class="action-hint">
                            *"Great things take time, just like nurturing leadership in our students."*
                        </div>
                    </div>
                </div>
            `);


            if (data && data.length > 0) {
                const container = $('.student-rep-assignment-status');
                container.html(`<div class="row" id="student-rep-view"></div>`);

                const class_advisors_view = $('#student-rep-view');
                data.forEach(item => {
                    const full_name = item.student_full_name || 'Not Assigned';
                    let roleElement = `
                    <div class="col col-2 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="card full-width">
                        <div class="row">
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                <div class="section-header-title text-left">${item.year_of_study_title} - Year </div>
                            </div>
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                <div class="section-header-title text-right">${item.section_title} - Section</div>
                            </div>
                        </div>
                            <div class="auth-img text-center">
                                <!-- Conditionally set the image source -->
                                <img src="${item.profile_pic ? '<?= GLOBAL_PATH . '/uploads/student_profile_pic/' ?>' + item.profile_pic : '<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>'}" alt="">
                            </div>
                            <div class="details text-center">
                                <h3 class="text-dark value">${full_name}</h3>
                                <p class="text-light">${item.student_reg_number}</p>
                                
                            </div>
                        </div>
                    </div>
                `;

                    class_advisors_view.append(roleElement)
                });
            }
        }

        const populateIndividualClassAdvisorsData = (data = []) => {
            const container = $('#role-view-section');

            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="class-advisor-status">
                    <div class="action-title">Your Class Advisor Assignment</div>
                    <div class="advisor-assignment-status">
                        <p class="action-text">
                            "It looks like you haven’t been assigned as a class advisor just yet. Hang tight, we’re sure your turn will come soon!"
                        </p>
                        <div class="action-hint">
                            *Much like Harry Potter awaiting his letter, your role is on its way — it simply requires a little more time to reach you.*
                        </div>
                    </div>
                </div>
            `);

            if (data && data.length > 0) {
                $(".advisor-assignment-status").html(`<div class="row" id="class-advisors-view"></div>`);

                const class_advisors_view = $('#class-advisors-view');
                data.forEach(item => {
                    const year_of_study_title = item.year_of_study_title + ' - Year' || '';
                    const section_title = item.section_title + ' - Section' || '';
                    const effective_from = item.effective_from || '';
                    const effective_to = item.effective_to || '';
                    const academic_batch_title = item.academic_batch_title || '';
                    const roleElement = `
                    <div class="card full-width">
                        <div class="row">
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                                <div class="section-header-title text-left ">${year_of_study_title}</div>
                            </div>
                            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="section-header-title text-right ">${section_title}</div>
                            </div>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Acadmic Batch</div>
                                <div class="value">${academic_batch_title}</div>
                            </div>
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective From</div>
                                <div class="value">${effective_from}</div>
                            </div>
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective To</div>
                                <div class="value">${effective_to}</div>
                            </div>
                        </div>
                    </div>
                `;

                    class_advisors_view.append(roleElement)
                });
            }
        }

        const populateClassAdvisorsEditData = (data = []) => {
            const container = $('#role-edit-section');
            container.empty()
            $('#role-edit-section').append(`
                <form id="dept-class-advisors-form" method="POST">
                    <div id="class-advisors"></div>
                    <div class="text-center">
                        <button type="submit" class="primary text-center full-width">SUBMIT</button>
                    </div>
                </form>
            `)

            const class_advisors_container = $('#class-advisors');
            data.forEach(item => {
                const full_name = item.salutation + ' ' + item.faculty_full_name;
                const faculty_id = item.faculty_id || "";
                const faculty_class_advisors_id = item.faculty_class_advisors_id || "";
                let roleElement = `
                <div class="row">
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                        <div class="section-header-title text-left">${item.year_of_study_title} - Year </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                        <div class="section-header-title text-right">${item.section_title} - Section</div>
                    </div>
                    <div class="input-container autocomplete-container">
                        <input type="text" class="auto faculty-class-advisors-name-dummy autocomplete-input" placeholder=" " value="${full_name}">
                        <label class="input-label">Select The Faculty Name</label>
                        <input type="hidden" name="faculty_id[]" class="faculty-id" value="${faculty_id}">
                        <input type="hidden" name="faculty_class_advisors_id[]" class="faculty-class-advisors-id" value="${faculty_class_advisors_id}">
                        <input type="hidden" name="ca_year_of_study_id[]" class="ca-year-of-study-id" value="${item.year_of_study_id}">
                        <input type="hidden" name="ca_section_id[]" class="ca-section-id" value="${item.section_id}">
                        <input type="hidden" name="faculty_dept_id[]" class="faculty-dept-id" value="${item.dept_id}">
                        <span class="autocomplete-arrow">&#8964;</span>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                </div>
                `;

                class_advisors_container.append(roleElement)


            });
            $('.faculty-class-advisors-name-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_name_list(element, <?= $logged_dept_id ?>);
            });



            $('.faculty-class-advisors-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });
            $('#dept-class-advisors-form').submit(function(e) {
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/dept_class_advisors_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: formData,
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        beforeSend: function() {
                            showComponentLoading(2)
                        },
                        success: function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        },
                        complete: function() {
                            hideComponentLoading(); // Hide the loading
                        }
                    });
                });
            });
        }

        const populateStudentRepresentativeEditData = (data = []) => {
            const container = $('#role-edit-section');
            container.empty()
            $('#role-edit-section').append(`
                <form id="dept-student-representative-form" method="POST">
                    <div id="student-representative"></div>
                    <div class="text-center">
                        <button type="submit" class="primary text-center full-width">SUBMIT</button>
                    </div>
                </form>
            `)

            const class_advisors_container = $('#student-representative');
            data.forEach(item => {
                const full_name = item.student_full_name || '';
                const student_id = item.student_id || "";
                const student_class_advisors_id = item.student_class_advisors_id || "";
                let roleElement = `
                <div class="row">
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                        <div class="section-header-title text-left">${item.year_of_study_title} - Year </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xl-6">
                        <div class="section-header-title text-right">${item.section_title} - Section</div>
                    </div>
                    <div class="input-container autocomplete-container">
                        <input type="text" class="auto student-representative-name-dummy autocomplete-input" placeholder=" " value="${full_name}">
                        <label class="input-label">Select The Student Name</label>
                        <input type="hidden" name="student_id[]" class="student-id" value="${student_id}">
                        <input type="hidden" name="student_representative_id[]" class="student-representative-id" value="${item.student_representative_id}">
                        <input type="hidden" name="rep_year_of_study_id[]" class="rep-year-of-study-id" value="${item.year_of_study_id}">
                        <input type="hidden" name="rep_section_id[]" class="rep-section-id" value="${item.section_id}">
                        <input type="hidden" name="rep_dept_id[]" class="rep-dept-id" value="${item.dept_id}">
                        <span class="autocomplete-arrow">&#8964;</span>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                </div>
                `;

                class_advisors_container.append(roleElement)


            });
            $('.student-representative-name-dummy').on('click', function() {
                const element = $(this);
                const dept_id = element.siblings('.student-dept-id').val();
                const year_of_study_id = element.siblings('.rep-year-of-study-id').val();
                const section_id = element.siblings('.rep-section-id').val();
                fetch_student_name_list(element, dept_id, year_of_study_id, section_id, 0);

            });



            $('.student-representative-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });
            $('#dept-student-representative-form').submit(function(e) {
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/dept_student_representative_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: formData,
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        beforeSend: function() {
                            showComponentLoading(2)
                        },
                        success: function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        },
                        complete: function() {
                            hideComponentLoading(); // Hide the loading
                        }
                    });
                });
            });
        }


        const populateIndividualRolesData = (data = []) => {
            // Clear any existing content


            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="department-committees-list">
                    <div class="action-title">Your Committees</div>
                    <div class="committees-overview">
                        <p class="action-text">
                            Committee participation records are on the horizon. Your leadership journey is just getting started!
                        </p>

                        <div class="action-hint">
                        *Every great journey begins with a single step. Stay tuned as the story of leadership unfolds.*
                        </div>
                    </div>
                </div>
            `);

            if (data && data.length > 0) {
                const viewRolesContainer = $('.committees-overview');
                viewRolesContainer.empty();
                // Iterate through each role data and create HTML structure
                data.forEach((role) => {
                    // Create HTML structure for each role
                    const roleHtml = `
          
                        <div class="card full-width">
                        <div class="section-header-title text-left ">${role.committee_title}</div>
                        <div class="row">
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Role</div>
                                <div class="value">${role.committee_role}</div>
                            </div>
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective From</div>
                                <div class="value">${role.effective_from}</div>
                            </div>
                            <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12  committee-section">
                                <div class="title">Effective To</div>
                                <div class="value">${role.effective_to}</div>
                            </div>
                        </div>
                    </div>
                `;

                    // Append the roleHtml to the container
                    viewRolesContainer.append(roleHtml);
                });
            }
        };

        const populateRolesData = (data = []) => {


            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="department-committees-list">
                    <div class="action-title">Department Committees Panel</div>
                    <div class="committees-overview">
                        <p class="action-text">
                            No committees have been formed yet.
                        </p>
                        <div class="action-hint">
                           *Great teams aren't born; they're assembled. The best is yet to come.*
                        </div>
                    </div>
                </div>
            `);

            if (data && data.length > 0) {
                const viewRolesContainer = $('.committees-overview');
                viewRolesContainer.empty();

                viewRolesContainer.html(`
                    <div class="row" id="faculty-committees-roles-list"></div>
                `);
                // Iterate through each role data and create HTML structure

                data.forEach((role) => {
                    const full_name = role.full_name || "";
                    const committee_role = role.committee_role || "";
                    const committee_title = role.committee_title || "";
                    const department_name = role.department_name || "";
                    const profile_pic_path = role.profile_pic_path || "";
                    const designation = role.designation || "";
                    // Determine the role name based on committee_role value

                    // Create HTML structure for each role
                    const roleHtml = `
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">         
                            <div class="card full-width">
                                <div class="row">
                                    <div class="col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                        <div class="section-header-title text-left">${committee_title}</div>
                                    </div>
                                    <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="section-header-title text-right">${committee_role}</div>
                                    </div>
                                </div>
                                <div class="auth-img text-center">
                                    
                                        <img src="${profile_pic_path ? '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + profile_pic_path : '<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>'}" alt=""> 
                                </div>
                                <div class="details text-center">
                                    <h3 class="text-dark  value">${full_name}</h3>
                                    <p class="text-light">${designation}</p>
                                    <p class="text-light">${department_name}</p>

                                </div>
                            </div>
                        </div>
                        `;

                    // Append the roleHtml to the container
                    $("#faculty-committees-roles-list").append(roleHtml);
                });
            }
        };

        const populateStudentRolesData = (data = []) => {


            $('#role-view-section').html(`
                <div class="main-content-card action-box" id="department-committees-list">
                    <div class="action-title">Student's Committees Panel</div>
                    <div class="committees-overview">
                        <p class="action-text">
                            "The student committees for your department are in the process of being organized. Stay tuned for updates on the roles and responsibilities."
                        </p>
                        <div class="action-hint">
                        *"Every great journey begins with a plan. The future leaders of your department are being shaped."*
                        </div>
                    </div>
                </div>
            `);


            if (data && data.length > 0) {
                const viewRolesContainer = $('.committees-overview');
                viewRolesContainer.empty();

                viewRolesContainer.html(`
                    <div class="row" id="faculty-committees-roles-list"></div>
                `);
                // Iterate through each role data and create HTML structure

                data.forEach((role) => {
                    const full_name = role.full_name || "";
                    const committee_role = role.committee_role || "";
                    const committee_title = role.committee_title || "";
                    const profile_pic_path = role.profile_pic_path || "";
                    const register_number = role.register_number || "";
                    // Determine the role name based on committee_role value

                    // Create HTML structure for each role
                    const roleHtml = `
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">         
                            <div class="card full-width">
                                <div class="row">
                                    <div class="col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                        <div class="section-header-title text-left">${committee_title}</div>
                                    </div>
                                    <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="section-header-title text-right">${committee_role}</div>
                                    </div>
                                </div>
                                <div class="auth-img text-center">
                                    
                                        <img src="${profile_pic_path ? '<?= GLOBAL_PATH . '/uploads/student_profile_pic/' ?>' + profile_pic_path : '<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>'}" alt=""> 
                                </div>
                                <div class="details text-center">
                                    <h3 class="text-dark  value">${full_name}</h3>
                                    <p class="text-light">${register_number}</p>

                                </div>
                            </div>
                        </div>
                        `;

                    // Append the roleHtml to the container
                    $("#faculty-committees-roles-list").append(roleHtml);
                });
            }
        };


        // Function to get the title from role_list based on value
        const getRoleTitleByValue = (value) => {
            if (value == 0 || value == '') {
                return ""; // Return an empty string if the value is 0
            }
            const role = role_list.find(role => role.value == value);
            return role ? role.title : 'Unknown Role'; // Return the title if found, otherwise return 'Unknown Role'
        };


        // Populate the form with committee data and existing faculty data
        const populateCommitteeForm = (committees, facultyData) => {
            $('#role-edit-section').empty()

            $('#role-edit-section').append(`
                <form id="dept-commitee-roles-form" method="POST">
                    <div class="committees"></div>
                    <div class="text-center">
                        <button type="submit" class="primary text-center full-width">SUBMIT</button>
                    </div>
                </form>
            `)
            const form = $('.committees');
            committees.forEach((committee) => {

                // Create a committee div without faculty fields initially
                const committeeHtml = `
                    <h3 class="section-header-title  text-left">${committee.title}</h3>
                    <div class="committee-group" data-committee-id="${committee.value}">
                        <!-- Faculty fields will be added here based on existing data -->
                        <div class="existing-faculty-fields"></div>
                        <div class="add-another-faculty flex-container align-center underline">
                            <span class="add-faculty" data-committee-id="${committee.value}">Add Another Faculty
                                <button type="button" class="icon tertiary add-faculty-btn">+</button>
                            </span>
                        </div>
                    </div>
                `;
                form.append(committeeHtml);

                // Populate existing faculty data for the committee
                const existingFaculty = facultyData.filter(faculty => faculty.committee_title == committee.value);

                if (existingFaculty.length > 0) {
                    // If there are existing faculty members, populate the fields with their data
                    existingFaculty.forEach(faculty => {
                        addFacultyField(committee.value, faculty.faculty_id, faculty.full_name, faculty.committee_role, faculty.faculty_roles_and_responsibilities_id);
                    });
                } else {
                    // If no existing faculty, add a single empty faculty field
                    addFacultyField(committee.value);
                }


            });

            $('.faculty-committee-name-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_name_list(element, <?= $logged_dept_id ?>);
            });

            $('.commitee-roles-dummy').on('click focus', function() {
                const element = $(this);
                const suggestions = element.siblings(".dropdown-suggestions");
                const value = element.siblings(".commitee-roles");
                // Pass the filtered list to showSuggestions
                showSuggestions(role_list, suggestions, value, element);
            });

            $('.faculty-committee-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });
            // Add click event listener for adding more faculty fields
            $('.add-faculty').on('click', function() {
                const committeeId = $(this).data('committee-id');
                addFacultyField(committeeId); // Add a new faculty field for the clicked committee
            });
            $('#dept-commitee-roles-form').submit(function(e) {
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/dept_commitee_roles_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: formData,
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        beforeSend: function() {
                            showComponentLoading(2)
                        },
                        success: function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        },
                        complete: function() {
                            hideComponentLoading(); // Hide the loading
                        }
                    });
                });
            });
        };

        // Populate the form with committee data and existing faculty data
        const populateStudentCommitteeForm = (committees = [], studentData = []) => {
            $('#role-edit-section').empty()

            $('#role-edit-section').append(`
                <form id="dept-student-commitee-roles-form" method="POST">
                    <div class="committees"></div>
                    <div class="text-center">
                        <button type="submit" class="primary text-center full-width">SUBMIT</button>
                    </div>
                </form>
            `)
            const form = $('.committees');
            committees.forEach((committee) => {
                // Create a committee div without student fields initially
                const committeeHtml = `
                    <h3 class="section-header-title  text-left">${committee.title}</h3>
                    <div class="committee-group" data-committee-id="${committee.value}">
                        <!-- student fields will be added here based on existing data -->
                        <div class="existing-student-fields"></div>
                        <div class="add-another-student flex-container align-center underline">
                            <span class="add-student" data-committee-id="${committee.value}">Add Another student
                                <button type="button" class="icon tertiary add-student-btn">+</button>
                            </span>
                        </div>
                    </div>
                `;
                form.append(committeeHtml);
                // Populate existing student data for the committee
                const existingstudent = studentData.filter(student => student.committee_id == committee.value);

                if (existingstudent.length > 0) {
                    // If there are existing student members, populate the fields with their data
                    existingstudent.forEach(student => {


                        addStudentField(committee.value, student.student_id, student.full_name, student.committee_role_id, student.student_committee_id);
                    });
                } else {
                    // If no existing student, add a single empty student field
                    addStudentField(committee.value);
                }


            });

            $('.student-committee-name-dummy').on('click', function() {
                const element = $(this);
                fetch_student_name_list(element, <?= $logged_dept_id ?>);
            });

            $('.commitee-roles-dummy').on('click focus', function() {
                const element = $(this);
                const suggestions = element.siblings(".dropdown-suggestions");
                const value = element.siblings(".commitee-roles");
                // Pass the filtered list to showSuggestions
                showSuggestions(role_list, suggestions, value, element);
            });

            $('.student-committee-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".student-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter student_name_list based on the input
                const filteredstudentList = student_name_list.filter(student =>
                    student.title.toLowerCase().includes(inputText) ||
                    student.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredstudentList, suggestions, value, element);
            });
            // Add click event listener for adding more student fields
            $('.add-student').on('click', function() {
                const committeeId = $(this).data('committee-id');
                addStudentField(committeeId); // Add a new student field for the clicked committee
            });
            $('#dept-student-commitee-roles-form').submit(function(e) {
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/dept_student_commitee_roles_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: formData,
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        beforeSend: function() {
                            showComponentLoading(2)
                        },
                        success: function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        },
                        complete: function() {
                            hideComponentLoading(); // Hide the loading
                        }
                    });
                });
            });
        };

        // Function to add another faculty field for a specific committee
        const addFacultyField = (committeeId, facultyId = null, fullName = '', committeeRole = '', r_r_id = 0) => {
            const committeeGroup = $(`.committee-group[data-committee-id="${committeeId}"] .existing-faculty-fields`);
            const roleTitle = getRoleTitleByValue(committeeRole); // Get role title based on committeeRole value

            const newFieldHtml = `
        <div class="row mt-2 faculty-row">
            <div class="col col-4 col-lg-5 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container autocomplete-container">
                    <input type="text" class="auto faculty-committee-name-dummy autocomplete-input" placeholder=" " value="${fullName}">
                    <label class="input-label">Select The Faculty Name</label>
                    <input type="hidden" name="faculty_name[]" class="faculty-id" value="${facultyId || 0}">
                    <span class="autocomplete-arrow">&#8964;</span>
                    <div class="autocomplete-suggestions"></div>
                </div>
            </div>
            <div class="col col-4 col-lg-5 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" class="auto commitee-roles-dummy dropdown-input" placeholder=" " value="${roleTitle}">
                    <label class="input-label">Select The Faculty Role</label>
                    <input type="hidden" name="commitee_roles[]" class="commitee-roles" value="${committeeRole}">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="dropdown-suggestions"></div>
                </div>
                <input type="hidden" class="committee-id" name="committee_id[]" value="${committeeId}">
            </div>
            <div class="col col-4 col-lg-2 col-md-6 col-sm-6 col-xs-12">
                <button type="button" class="icon tertiary remove-faculty-role-btn" data-faculty_roles_and_responsibilities_id="${r_r_id}">X</button>
            </div>
        </div>
    `;
            committeeGroup.append(newFieldHtml);



            // Attach event listener to the remove button within the added row
            committeeGroup.find('.remove-faculty-role-btn').last().on('click', function() {
                const row = $(this).closest('.row');
                const facultyId = row.find('.faculty-id').val();
                const role = row.find('.commitee-roles').val();
                const r_r_id = $(this).data('faculty_roles_and_responsibilities_id');
                remove_faculty_role(r_r_id)

                // Remove the row from the DOM
                row.remove();
            });
            // Attach input events for the new fields
            $('.faculty-committee-name-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_name_list(element, <?= $logged_dept_id ?>);
            });

            $('.commitee-roles-dummy').on('click focus', function() {
                const element = $(this);
                const suggestions = element.siblings(".dropdown-suggestions");
                const value = element.siblings(".commitee-roles");
                // Pass the filtered list to showSuggestions
                showSuggestions(role_list, suggestions, value, element);
            });

            $('.faculty-committee-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });
        };

        // Function to add another faculty field for a specific committee
        const addStudentField = (committeeId, studentId = null, fullName = '', committeeRole = '', r_r_id = 0) => {
            const committeeGroup = $(`.committee-group[data-committee-id="${committeeId}"] .existing-student-fields`);
            const roleTitle = getRoleTitleByValue(committeeRole); // Get role title based on committeeRole value

            const newFieldHtml = `
        <div class="row mt-2 student-row">
            <div class="col col-4 col-lg-5 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container autocomplete-container">
                    <input type="text" class="auto student-committee-name-dummy autocomplete-input" placeholder=" " value="${fullName}">
                    <label class="input-label">Select The student Name</label>
                    <input type="hidden" name="student_name[]" class="student-id" value="${studentId || 0}">
                    <span class="autocomplete-arrow">&#8964;</span>
                    <div class="autocomplete-suggestions"></div>
                </div>
            </div>
            <div class="col col-4 col-lg-5 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" class="auto commitee-roles-dummy dropdown-input" placeholder=" " value="${roleTitle}">
                    <label class="input-label">Select The student Role</label>
                    <input type="hidden" name="commitee_roles[]" class="commitee-roles" value="${committeeRole}">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="dropdown-suggestions"></div>
                </div>
                <input type="hidden" class="committee-id" name="committee_id[]" value="${committeeId}">
            </div>
            <div class="col col-4 col-lg-2 col-md-6 col-sm-6 col-xs-12">
                <button type="button" class="icon tertiary remove-student-role-btn" data-student_roles_and_responsibilities_id="${r_r_id}">X</button>
            </div>
        </div>
    `;
            committeeGroup.append(newFieldHtml);



            // Attach event listener to the remove button within the added row
            committeeGroup.find('.remove-student-role-btn').last().on('click', function() {
                const row = $(this).closest('.row');
                const studentId = row.find('.student-id').val();
                const role = row.find('.commitee-roles').val();
                const r_r_id = $(this).data('student_roles_and_responsibilities_id');
                remove_student_role(r_r_id)

                // Remove the row from the DOM
                row.remove();
            });
            // Attach input events for the new fields
            $('.student-committee-name-dummy').on('click', function() {
                const element = $(this);
                fetch_student_name_list(element, <?= $logged_dept_id ?>);
            });

            $('.commitee-roles-dummy').on('click focus', function() {
                const element = $(this);
                const suggestions = element.siblings(".dropdown-suggestions");
                const value = element.siblings(".commitee-roles");
                // Pass the filtered list to showSuggestions
                showSuggestions(role_list, suggestions, value, element);
            });

            $('.student-committee-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".student-id");

                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter student_name_list based on the input
                const filteredstudentList = student_name_list.filter(student =>
                    student.title.toLowerCase().includes(inputText) ||
                    student.code.toLowerCase().includes(inputText)
                );

                // Pass the filtered list to showSuggestions
                showSuggestions(filteredstudentList, suggestions, value, element);
            });
        };


        const remove_faculty_role = (r_r_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/remove_faculty_role.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'r_r_id': r_r_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }

        const remove_student_role = (r_r_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/remove_student_role.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'r_r_id': r_r_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            resolve(response.message);
                        } else {
                            showToast(response.status, response.message);
                            reject(response.message);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }

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
                        response = JSON.parse(response)
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
        // Function to update the heading based on the action and last part of the URL path
        const callAction = (element) => {
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

            // Remove any file extension if present (e.g., '.php' from 'faculty-roles-responsibilities.php')
            const cleanPath = lastPath.split('.')[0];

            title += cleanPath.replace(/-/g, ' '); // Replace hyphens with spaces for readability

            // Update the <h2> tag with the formatted title
            element.text(title);
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
                            const value = element.siblings(".committee-dept-filter")
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

        const fetch_year_of_study = (element, dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_year_of_study.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const year_data = response.data
                            const dept_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".year-of-study-filter")
                            showSuggestions(year_data, suggestions, value, element);
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

        const loadComponentsBasedOnURL = async () => {

            const urlParams = new URLSearchParams(window.location.search);
            const route = urlParams.get('route');
            const action = urlParams.get('action');
            var type = urlParams.get('type'); // e.g., 'add', 'edit'
            try {
                if (!action) {
                    // No action specified, call roleView by default
                    await roleView();
                } else if (action == 'view') {

                    $('#faculty-role-edit').empty()
                    if (route == 'faculty') {

                        await roleView();
                        await loadBgCard();

                        await loadBreadcrumbs();
                    } else if (route == 'student') {
                        if (type != "committees") {
                            type = "committees";
                        }
                        const params = {
                            action: action,
                            route: route,
                            type: type
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${action}&route=${route}&type=${type}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;

                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        await roleStudentView();
                        await loadBgCard();

                        await loadBreadcrumbs();
                    }
                } else if (action == 'edit') {
                    $('#faculty-role-view').empty()
                    await loadBreadcrumbs();
                    if (route == 'faculty') {
                        roleEdit()
                    } else if (route == 'student') {
                        roleStudentEdit()
                    }
                } else {
                    console.error("Unknown action");
                }

            } catch (error) {
                console.error('An error occurred while loading components:', error);
            }
        };
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>