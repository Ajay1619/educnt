<?php require_once('../../config/sparrow.php');  ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">

    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>">
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_achievements/css/achievements.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_achievements/css/create_achievements.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_achievements/css/edit_achievements.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_achievements/css/overall_achievements.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">
</head>

<body>
    <section id="Loading"></section>
    <section id="error-popup"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <main>
        <section id="Component-Loading"></section>
        <div id="toast-container"></div>

        <section id="bg-card"></section>
        <section id="faculty-achievement-popup"></section>
        <section id="achievements-fetch"></section>
        <section id="achievement-functions"></section>
    </main>
    <section id="footer"></section>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script src="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.js' ?>"></script>


    <script>
        const faculty_achievements_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/ajax/achievement_function.php', ENT_QUOTES, 'UTF-8') ?>',
                    beforeSend: function() {
                        showComponentLoading(2) // Show loading before request
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#achievement-functions').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                    complete: function() {

                        hideComponentLoading();

                    }
                });
            });
        };



        // Load all components on page load
        $(document).ready(async function() {
            try {
                await showLoading();
                await faculty_achievements_functions();
                await loadComponentsBasedOnURL();


                <?php if ($logged_profile_status == 1) { ?>

                    await loadSidebar();
                <?php }  ?>

                await loadTopbar();
                await loadFooter();
                await loadBgCard();
                await loadBreadcrumbs();



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


        $(document).on('click', '#add_btn', async function(event) {
            await showComponentLoading(1)
            event.preventDefault();
            params = '?action=add&route=faculty';
            const newUrl = window.location.origin + window.location.pathname + params;
            history.pushState({
                action: 'add',
                route: 'faculty'
            }, '', newUrl);
            await loadComponentsBasedOnURL();
            await callAction($("#action"));
            await hideComponentLoading();
        });
        $(document).on('click', '#view_btn', async function(event) {
            await showComponentLoading(1)
            event.preventDefault();
            params = '?action=view&route=faculty';
            const newUrl = window.location.origin + window.location.pathname + params;
            history.pushState({
                action: 'view',
                route: 'faculty'
            }, '', newUrl);
            await loadComponentsBasedOnURL();
            await callAction($("#action"));
            await hideComponentLoading();

        });




        // Run the function after DOM is ready
    </script>
</body>

</html>