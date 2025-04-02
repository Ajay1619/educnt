<?php require_once('../../../config/sparrow.php');
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
    if (isset($_GET['student_id']) && $_GET['student_id'] != 0) {
        $_SESSION['admission_student_existing'] =  sanitizeInput($_GET['student_id']);
    }
?>

    <section id="admission_student_page">
        <div class="staff_admission_container">
            <section id="stepper"></section>
            <br>
            <!-- <section id="tabs"></section> -->
            <section id="content"></section>

        </div>
        <div id="faculty-update-profile-functions"></div>
        <div id="faculty-update-education-functions"></div>
        <div id="faculty-update-admission-functions"></div>
        <div id="faculty-update-fees-functions"></div>
        <div id="faculty-update-course-functions"></div>
        <div id="faculty-update-document-functions"></div>
    </section>


    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <script>
        (function() {
            const student_update_profile_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_profile_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-profile-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            const student_update_course_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_course_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-course-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            const student_update_education_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_education_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-education-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            const student_update_document_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_document_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-document-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            const student_update_admission_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_admission_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-admission-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            const student_update_fees_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_fees_function.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#faculty-update-fees-functions').html(response);
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise
                        }
                    });
                });
            }
            //     const StepperProfileForm = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/stepper_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#stepper').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };
            // const TabsProfileForm = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/tabs_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#tabs').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };

            // const PersonalProfileForm = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= MODULES . '/faculty_student_admission/components/update_profile/profile_info/personal_information_faculty_student_admission.php' ?>',

            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#content').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };
            // const course_preference = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= MODULES . '/faculty_student_admission/components/update_profile/profile_info/personal_information_faculty_student_admission.php' ?>',

            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#content').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };
            // const EducationProfileForm = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/education_info/education_information_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#content').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };
            // const UploadProfileForm = () => {
            //     return new Promise((resolve, reject) => {
            //         $.ajax({
            //             type: 'GET',
            //             url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/upload_file_info/upload_information_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
            //             headers: {
            //                'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
            //             'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
            //             },
            //             success: function(response) {
            //                 $('#content').html(response);
            //                 resolve(); // Resolve the promise
            //             },
            //             error: function(jqXHR) {
            //                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            //                 showToast('error', message);
            //                 reject(); // Reject the promise
            //             }
            //         });
            //     });
            // };


            $(document).ready(async function() {
                try {

                    await student_update_profile_functions();
                    await student_update_education_functions();
                    await student_update_admission_functions();
                    await student_update_fees_functions();
                    await student_update_course_functions();
                    await student_update_document_functions();
                    await StepperProfileForm();
                    await TabsProfileForm();
                    // console.log(student_id);
                    await loadUrlBasedOnURL();
                    // loadComponentsBasedOnURL();



                } catch (error) {
                    console.error('An error occurred while loading:', error);
                }
            });





        })();

        // Run the function after DOM is ready
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
