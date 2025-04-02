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
        const student_admission_admitted = (studentId) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/admission_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'studentId': studentId
                    }, // Send roleId to the server
                    success: function(response) {
                        $('#student-admission-popup').html(response); // Load response into the edit element
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        // Handle different error messages based on the status code
                        const message = jqXHR.status == 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message); // Show error message
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const student_admission_edit = (student_id) => {
            const params = {
                action: 'add',
                route: 'faculty',
                type: 'personal',
                tab: 'personal',

            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            loadComponentsBasedOnURL(student_id);
        }

        const student_admission_declined = (studentId) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/admission_cancel_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'studentId': studentId
                    }, // Send roleId to the server
                    success: function(response) {
                        $('#student-admission-popup').html(response); // Load response into the edit element
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        // Handle different error messages based on the status code
                        const message = jqXHR.status == 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message); // Show error message
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const parent_account_creation = (studentId) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/parent_account_creation.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'studentId': studentId
                    },

                    success: function(response) {
                        $('#student-admission-popup').html(response); // Load response into the edit element
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        // Handle different error messages based on the status code
                        const message = jqXHR.status == 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message); // Show error message
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const fetch_new_student_account_code = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_new_student_account_code.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        // const username_data = response.username_data;
                        // const role_data = response.role_data;
                        const username = response.username;
                        const account_id = response.account_id;
                        if (response.code == 200) {
                            $('#student-username').val(username);
                            $('#account-id').val(account_id);
                            // $('#role-id').val(role_data.role_id);
                            // $('#portal-type').val(role_data.role_code);
                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            });
        }


        const fetch_parent_relation = (element) => {
            // Array of lateral entry options (No/Yes)
            const parent_relation = [{
                    title: "Father",
                    value: 1, // SQL column: 'student_know_about_us' - refers to the source of knowledge (1=Friends or Family)
                },
                {
                    title: "Mother",
                    value: 2, // SQL column: 'student_know_about_us' - refers to the source of knowledge (2=Social Media)
                },
                {
                    title: "Brother",
                    value: 3, // SQL column: 'student_know_about_us' - refers to the source of knowledge (3=Website)
                },
                {
                    title: "Sister",
                    value: 4, // SQL column: 'student_know_about_us' - refers to the source of knowledge (4=Advertisement)
                },
                {
                    title: "Spouse",
                    value: 5, // SQL column: 'student_know_about_us' - refers to the source of knowledge (5=Events or Workshops)
                },
                {
                    title: "Guardian",
                    value: 6, // SQL column: 'student_know_about_us' - refers to the source of knowledge (6=Other)
                }
            ];
            const parent_relation_data = parent_relation;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#relation-batch");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(parent_relation_data, suggestions, value, element);
        };
        const fetch_new_parent_account_code = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_new_parent_account_code.php' ?>',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        const username_data = response.username_data;
                        const role_data = response.role_data;
                        if (response.code == 200) {
                            $('#parent-user-name').val(username_data.prefix_title + username_data.new_account_code);
                            $('#parent-code').val(username_data.new_account_code);
                            $('#parent-role').val(role_data.role_id);
                            $('#parent-type').val(role_data.role_code);
                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            });
        }
        const fetch_academic_batch = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_academic_batch.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const achievement = response.data;

                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(achievement, $('#admission_batch_suggestion'), $('#admission-batch'), element);
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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
