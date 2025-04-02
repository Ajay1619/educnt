<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_student_fees/css/fees.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">

</head>

<body>
    <div id="toast-container"></div>
    <section id="Component-Loading"></section>
    <section id="Loading"></section>
    <section id="error-popup"></section>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>

    <main>
        <section id="bg_card"></section>
        <section id="main-components"></section>
        <section id="student-fees-popup"></section>


    </main>
    <section id="footer"></section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <section id="student-fees-functions"></section>

    <script>
        const load_student_fees_functions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/function/faculty_student_fees_details_function.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#student-fees-functions').html(response);
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
                await load_student_fees_functions();
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
                //  
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