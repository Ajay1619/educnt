<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= MODULES . '/faculty_student_admission/css/student_admission_info_staff_interview.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_student_admission/css/profile_view.css' ?>" />
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">


</head>

<body>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <section id="Loading"></section>
    <main>
        <div id="toast-container"></div>
        <section id="Component-Loading"></section>
        <section id="profile_view"></section>
        <section id="editpage"></section>
        <div id="student_admission_view"></div>
        <div id="student-admission-popup"></div>
        <section id="overall-admission-table-function"></section>
    </main>
    <section id="footer"></section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script src="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.js' ?>"></script>

    <script>
        // Show the loading screen

        const student_admission_view = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/student_admission_view_function.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#student_admission_view').html(response);
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
        const admission_overall_function = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/functions/admission_overall_function.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>',
                    },
                    success: function(response) {
                        $('#overall-admission-table-function').html(response);
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
        const loadSidebar = () => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
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
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
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


        // Load the top navigation bar


        // Load the side navigation bar

        const load_personal_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/profile_view.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        console.log("p");
                        $('#profile_view').html(response);
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/faculty_student_overall_admission.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#profile_view').html(response);
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
        const load_update_personal_profile = (student_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_id': student_id
                    },
                    success: function(response) {
                        $('#profile_view').html(response);
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

        const load_dashboard_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/dashboard/dashboard_profile.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#profile_view').html(response);
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


        $(document).ready(async function() {

            try {
                showLoading();
                await student_admission_view();
                await admission_overall_function();
                <?php if ($logged_profile_status == 1) { ?>

                    await loadSidebar();
                <?php }  ?>

                await loadTopbar();
                await loadFooter();
                await loadComponentsBasedOnURL();
                // await loadUrlBasedOnURL();


            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideLoading(); // Delay hiding loading by 1 second
                }, 1000)
            }
        });



        // Run the function after DOM is ready
    </script>


</body>

</html>