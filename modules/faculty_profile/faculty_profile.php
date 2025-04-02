<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">

    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= MODULES . '/faculty_profile/css/faculty_addmission_info_staff_interview.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_profile/css/profile_view.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_profile/css/profile_dashboard.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
</head>

<body>

    <section id="Loading"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <main>

        <section id="Component-Loading"></section>
        <section id="dashboards"></section>
        <div id="toast-container"></div>
        <section id="pv"></section>

    </main>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <section id="overall-functions"></section>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script src="<?= PACKAGES . '/apexchart/apexchart.js' ?>"></script>
    <script src="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.js' ?>"></script>

    <script>
        const load_overall_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/functions/profile_overall_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#overall-functions').html(response);
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

        $(document).ready(async function() {
            try {

                showLoading();
                await load_overall_functions();
                <?php if ($logged_profile_status == 1) { ?>

                    await loadSidebar();
                <?php }  ?>

                await loadTopbar();

                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action'); // e.g., 'add', 'edit'
                const route = urlParams.get('route'); // e.g., 'add', 'edit'
                const type = urlParams.get('type');
                const id = urlParams.get('id');

                if (action == "add") {
                    await load_update_personal_profile();
                } else if (action == "view" && route == "faculty" && !type) {
                    await load_personal_profile('<?= encrypt_data($logged_user_id) ?>')
                } else if (action == "view" && route == "faculty" && type == "overall" && !id) {
                    await load_overall_personal_profile()
                } else if (action == "view" && route == "faculty" && type == "overall" && id != '') {
                    await load_personal_profile(id)
                } else if (action == "view" && route == "student" && type == "overall" && !id) {
                    await load_overall_personal_faculty_student_profile()
                } else if (action == "view" && route == "student" && type == "overall" && id != '') {
                    await view_individual_student_profile(id)
                } else if (action == "view" && route == "faculty" && type == "dashboard") {
                    await load_faculty_profile_dashboard()
                } else if (action == "view" && route == "student" && type == "dashboard") {
                    await load_student_profile_dashboard()
                } else if (action == "view" && route == "student" && type == "overall") {
                    await load_overall_student_profile()
                }

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
    </script>

</body>

</html>