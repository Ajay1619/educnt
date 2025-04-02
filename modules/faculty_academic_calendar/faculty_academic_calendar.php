<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/faculty_academic_calendar/css/calendar.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />

</head>

<body>
    <section id="overall-functions"></section>
    <header>
        <section id="Loading"></section>
        <section id="Component-Loading"></section>
        <div id="toast-container"></div>

        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <main>
        <section id="bg_card"></section>
        <section id="event_table"></section> 
        <section id="academic-calendar-popup"></section>
        <section id="academic-calendar-event-popup-view"></section>
        <!-- <div class="row">
            <section id="calendar"></section>
        </div> -->

    </main>
    <section id="footer"></section>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script src="<?= PACKAGES . '/apexchart/apexchart.js' ?>"></script>
    <script src="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.js' ?>"></script>




    <script>
     
        const load_calendar_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/functions/academic_calendar_function.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#overall-functions').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }

        $(document).ready(async function() {

            try {
                showLoading();
                await load_calendar_functions();
                <?php if ($logged_profile_status === 1) { ?>

                    await loadSidebar();
                <?php }  ?>

                await loadTopbar();
                await loadFooter();
                await load_bg_card();
                await loadComponentsBasedOnURL();

                // const urlParams = new URLSearchParams(window.location.search);
                // const action = urlParams.get('action'); // e.g., 'add', 'edit'
                // const route = urlParams.get('route'); // e.g., 'add', 'edit'


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