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
        const loadprofileComponentsBasedOnURL = async (student_id) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'

            if (action == 'add' && route == 'faculty' && type == 'personal') {
                if (tab == 'personal') {
                    load_student_personal_profile_info_form(student_id);
                    $('.tab-btn.personal').addClass('active');
                    $('.tab-btn.contact').removeClass('active');
                    $('.tab-btn.parent').removeClass('active');
                    $('.tab-btn.address').removeClass('active');
                    $('.tab-btn.official').removeClass('active');
                } else if (tab == 'contact') {
                    load_student_contact_profile_info_form();
                    $('.tab-btn.personal').removeClass('active');
                    $('.tab-btn.contact').addClass('active');
                    $('.tab-btn.parent').removeClass('active');
                    $('.tab-btn.address').removeClass('active');
                    $('.tab-btn.official').removeClass('active');
                    // $('.tab-btn.personal').addClass('active').css('background-color', 'var(--success-dark)');
                } else if (tab == 'parent') {
                    load_student_parent_profile_info_form();
                    $('.tab-btn.personal').removeClass('active');
                    $('.tab-btn.contact').removeClass('active');
                    $('.tab-btn.parent').addClass('active');
                    $('.tab-btn.address').removeClass('active');
                    $('.tab-btn.official').removeClass('active');
                    // $('.tab-btn.personal').addClass('active').css('background-color', 'var(--success-dark)');
                } else if (tab == 'address') {
                    load_student_address_profile_info_form();
                    $('.tab-btn.personal').removeClass('active');
                    $('.tab-btn.contact').removeClass('active');
                    $('.tab-btn.parent').removeClass('active');
                    $('.tab-btn.address').addClass('active');
                    $('.tab-btn.official').removeClass('active');
                } else if (tab == 'official') {
                    load_student_official_profile_info_form();
                    $('.tab-btn.personal').removeClass('active');
                    $('.tab-btn.contact').removeClass('active');
                    $('.tab-btn.parent').removeClass('active');
                    $('.tab-btn.address').removeClass('active');
                    $('.tab-btn.official').addClass('active');
                }
            } else {
                console.error('No matching condition for route and action');
            }
        };


        const load_personal_info_components = async (student_id) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'

            if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'personal') {
                await load_personal_profile_info_form(student_id);
                $('.tab-btn.personal').addClass('active');
                $('.tab-btn.contact').removeClass('active');
                $('.tab-btn.address').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'contact') {
                await load_contact_profile_info_form();
                $('.tab-btn.contact').addClass('active');
                $('.tab-btn.personal').removeClass('active');
                $('.tab-btn.address').removeClass('active');
            } else if (action == 'add' && route == 'faculty' && type == 'personal' && tab == 'address') {
                await load_address_profile_info_form();
                $('.tab-btn.address').addClass('active');
                $('.tab-btn.contact').removeClass('active');
                $('.tab-btn.personal').removeClass('active');
            }
        }

        const loadUrlBasedOnURL = async (student_id) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'

            //   $('.step.educationstep').removeClass('active');
            //   $('.step.coursestep').removeClass('active');
            //   $('.step.documentuploadstep').removeClass('active');

            if (route == 'faculty' && action == 'add' && type == 'personal') {


                $('.step.personalstep').addClass('active');
                $('.step.educationstep').removeClass('active');
                $('.step.coursestep').removeClass('active');
                $('.step.feesstep').removeClass('active');
                $('.step.documentuploadstep').removeClass('active');
                PersonalProfileForm(`?action=add&route=student&type=personal`);

            } else if (route == 'faculty' && action == 'add' && type == 'education') {
                EducationProfileForm(`?action=add&route=student&type=education`);
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.coursestep').removeClass('active');
                $('.step.feesstep').removeClass('active');
                $('.step.documentuploadstep').removeClass('active');
            } else if (route == 'faculty' && action == 'add' && type == 'course') {
                course_preference(`?action=add&route=student&type=course`);
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.coursestep').addClass('active');
                $('.step.feesstep').removeClass('active');
                $('.step.documentuploadstep').removeClass('active');

            } else if (route == 'faculty' && action == 'add' && type == 'fees') {
                student_fees_details(`?action=add&route=student&type=fees`);
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.coursestep').addClass('active');
                $('.step.feesstep').addClass('active');
                $('.step.documentuploadstep').removeClass('active');

            } else if (route == 'faculty' && action == 'add' && type == 'documentupload') {
                UploadProfileForm(`?action=add&route=student&type=documentupload`);
                $('.step.personalstep').addClass('active');
                $('.step.educationstep').addClass('active');
                $('.step.coursestep').addClass('active');
                $('.step.feesstep').addClass('active');
                $('.step.documentuploadstep').addClass('active');
            } else {
                console.error('nodatafound');
            }
            // $('#nxt_btn').on('click', function(e) {

            //     e.preventDefault(); // Prevent default button behavior

            //     // Initialize variable for new parameters
            //     let params = '';
            //     if (action == 'add' && route == 'faculty' && type == 'personal') {
            //         if (!tab || tab == '') {
            //             params = '?action=add&route=student&type=personal&tab=personal';
            //         } else if (tab == 'personal') {
            //             params = '?action=add&route=student&type=personal&tab=contact';
            //         } else if (tab == 'contact') {
            //             params = '?action=add&route=student&type=personal&tab=parent';
            //         } else if (tab == 'parent') {
            //             params = '?action=add&route=student&type=personal&tab=address';
            //         }
            //         else if (tab == 'address') {
            //             // Looping back to personal (optional)
            //             params = '?action=add&route=student&type=education&tab=sslc';
            //         }
            //     } else if (action == 'add' && route == 'faculty' && type == 'education') {
            //         if (tab == 'sslc') {
            //             params = '?action=add&route=student&type=education&tab=hsc';
            //         } else if (tab == 'hsc') {
            //             params = '?action=add&route=student&type=education&tab=diploma';

            //         } else if (tab == 'diploma') {
            //             params = '?action=add&route=student&type=education&tab=ug';

            //         } else if (tab == 'ug') {
            //             params = '?action=add&route=student&type=education&tab=pg';

            //         } else if (tab == 'pg') {
            //             params = '?action=add&route=student&type=experience&tab=industry';

            //         }
            //     }  else if (action == 'add' && route == 'faculty' && type == 'skill') {
            //         if (tab == 'knowledge') {
            //             params = '?action=add&route=student&type=documentupload&tab=document';
            //         } else if (tab == 'institution') {
            //             params = '?action=add&route=student&type=skill&tab=knowledge';

            //         }
            //     } else if (action == 'add' && route == 'faculty' && type == 'documentupload') {
            //         if (tab == 'document') {
            //             params = '?action=add&route=student&type=documentupload&tab=document';
            //         } else if (tab == 'institution') {
            //             params = '?action=add&route=student&type=skill&tab=knowledge';

            //         }
            //     } else {
            //         console.log('No matching condition for route and action');
            //         return;
            //     }

            //     // Construct the new URL by appending parameters
            //     const newUrl = window.location.origin + window.location.pathname + params;

            //     // Use history.pushState to update the URL without refreshing the page
            //     history.pushState({
            //         action: 'add',
            //         route: 'student'
            //     }, '', newUrl);


            //     // Manually trigger component rendering based on new URL
            //     loadUrlBasedOnURL();
            // });



        }
        //  else {
        //     console.log("hi");
        // }
        const StepperProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/stepper_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token
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
        const TabsProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/tabs_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#tabs').html(response);
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
                    url: '<?= MODULES . '/faculty_student_admission/components/update_profile/profile_info/personal_information_faculty_student_admission.php' ?>',

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
        const PersonalfeesForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/components/update_profile/profile_info/student_admission_fees_tabs.php' ?>',

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
        const course_preference = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/components/update_profile/course_preference/course_information_faculty_student_admission.php' ?>',

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
        const student_fees_details = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= MODULES . '/faculty_student_admission/components/update_profile/fees_info/student_admission_fees_tabs.php' ?>',

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
        const UploadProfileForm = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/upload_file_info/upload_information_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
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

        const load_student_personal_profile_info_form = (student_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/personal_profile_info_faculty_student_admission.php?action=add&route=personal&type=personal', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'student_id': student_id
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
        const load_student_contact_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/contact_profile_info_faculty_student_admission.php?action=add&route=personal&type=contact', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
        const load_student_official_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/official_profile_info_faculty_student_admission.php?action=add&route=personal&type=official', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
        const load_student_parent_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/parent_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
        const fetch_official_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_official_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        $('#official-mail-id').val(data.official_mail_id);
                        $('#register-number').val(data.student_reg_number);


                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }
        const load_student_address_profile_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/profile_info/address_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
