<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_lesson_plan/css/lesson_plan.css' ?>" />

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
 
</head>

<body>
    <div id="toast-container"></div>
    <section id="Loading"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>

    <main>
    <section id="Component-Loading"></section>
        <div id="toast-container"></div>
        <section id="bg_card"></section>
        <section id="lesson-plan-list"></section>
        <section id="add-lesson-plan"></section>
        <section id="lesson-plan-list-popup-view"></section>


    </main>
    <section id="footer"></section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <section id="lesson-plan-functions"></section>

    <script>
        const load_lesson_plan_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/functions/faculty_lesson_plan_function.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#lesson-plan-functions').html(response);
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
                await load_lesson_plan_functions();


                await loadSidebar();

                await loadTopbar();
                await loadBgCard();
                await loadBreadcrumbs();
                await loadFooter();
                await callAction();

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