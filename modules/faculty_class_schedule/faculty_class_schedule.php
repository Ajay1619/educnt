<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_class_schedule/css/timetable_allotting.css' ?>" />

</head>

<body>
    <div id="toast-container"></div>
    <section id="Loading"></section>
    <section id="Component-Loading"></section>
    <section id="error_timetable"></section>
    <section id="success_timetable"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>

    <main>
        <section id="class-schedule-bg-card"></section>
        <section id="class-schedules-popup"></section>
        <section id="main-components"></section>
    </main>
    <section id="footer"></section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <section id="schedule-overall-functions"></section>

    <script>
        const load_schedule_overall_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/ajax/schedule_overall_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#schedule-overall-functions').html(response);
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
                await load_schedule_overall_functions();
                await loadSidebar();
                await loadTopbar();
                await loadBgCard();
                await loadBreadcrumbs();
                await loadFooter();
                await load_main_components();
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