<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">

    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/faculty_classes/css/faculty_classes.css' ?>">
</head>

<body>
    <section id="Loading"></section>
    <section id="error-popup"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <main>
        <section id="faculty-classes-popup"></section>
        <section id="Component-Loading"></section>
        <div id="toast-container"></div>
        <section id="bg-card"></section>
        <section id="faculty-classes-overall-functions"></section>
        <section id="faculty-classes-main-content"></section>


    </main>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script>
        const load_faculty_classes_overall_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_classes/ajax/faculty_classes_overall_functions.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        location: window.location.href
                    },
                    success: function(response) {
                        $('#faculty-classes-overall-functions').html(response);
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



        $(document).ready(async function() {
            try {
                showLoading();
                await load_faculty_classes_overall_functions();
                // Load initial components
                await loadSidebar();
                await loadTopbar();
                await loadBgCard();

                await loadBreadcrumbs();
                await load_faculty_classes_main_content();

                // Call the function to update the heading
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