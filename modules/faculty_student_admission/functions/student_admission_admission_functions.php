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
        const load_course_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/course_preference/course_preference_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // SecureCSRFtoken // Secure CSRF token // Secure CSRF token
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
        const load_document = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/upload_file_info/upload_information_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
        const NavigationProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/navigation_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#navigation').html(response);
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

        //     $(document).ready(function() {
        //     const urlParams = new URLSearchParams(window.location.search);
        //     const tab = urlParams.get('tab'); // Get the current tab from the URL

        //     // Remove 'active' from all buttons first
        //     $('.tab-btn').removeClass('active');

        //     // Add 'active' to the corresponding button based on the tab value
        //     if (tab == 'personal') {
        //         $('.tab-btn.personal').addClass('active');
        //     } else if (tab == 'contact') {
        //         $('.tab-btn.contact').addClass('active');
        //     } else if (tab == 'address') {
        //         $('.tab-btn.address').addClass('active');
        //     }
        // });
        //     $(document).ready(async function() {
        //     try {
        //         // await NavigationProfileForm();
        //         const urlParams = new URLSearchParams(window.location.search);
        //         const route = urlParams.get('route');
        //         const action = urlParams.get('action');
        //         const type = urlParams.get('type');
        //         const tab = urlParams.get('tab');

        //         $('.tab-btn').removeClass('active');
        //         // Condition to load the correct form based on URL parameters


        //         // Call the function directly, no need for another $(document).ready
        //         await loadComponentsBasedOnURL();

        //     } catch (error) {
        //         console.error('An error occurred while processing:', error);
        //     }
        // });

        const load_update_admission_profile_components = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const route = urlParams.get('route');
            const action = urlParams.get('action');
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');

            // Condition to load the correct form based on URL parameters
            if (action == 'add' && route == 'faculty' && type == 'course') {
                if (tab == 'course') {
                    load_course_info_form();
                } else {
                    console.error('No matching condition for route and action');
                }
            } else if (action == 'add' && route == 'faculty' && type == 'fees') {
                if (tab == 'fees_details') {
                    student_fees_details();
                }
            } else if (action == 'add' && route == 'faculty' && type == 'fees') {
                if (tab == 'concession_details') {
                    student_concession_fees_details();
                }
            }
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
