<?php require_once('../../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title><?= $currentLocation ?></title>
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_admission/css/faculty_admission.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_admission/css/overall_achievements.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">


</head>

<body>
    <header>
        <section id="topbar"></section>
        <section id="sidebar"></section>
    </header>
    <main>
        <section id="Loading">
            <div class="overlay">
                <div class="loading-container">
                    <div class="half"></div>
                    <div class="half"></div>
                </div>
                <div class="loading-text">
                    LOADING<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
                </div>
            </div>
        </section>
        <section id="bg-card"></section>
        <section id="faculty-admission"></section>
        <section id="overall-faculty-admission"></section>
        <section id="overall_faculty_admission_function"></section>

    </main>
    <section id="footer"></section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>




    <script>
        const load_overall_faculty_admission_function = () => {
return new Promise((resolve, reject) => {
    $.ajax({
        type: 'GET',
        url: '<?= htmlspecialchars(MODULES . '/faculty_admission/components/function/faculty_admission_function.php', ENT_QUOTES, 'UTF-8') ?>',
        beforeSend: function() {
            showLoading();
        },
        headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
            'X-Requested-Path': window.location.pathname + window.location.search
        },
        success: function(response) {
            $('#overall_faculty_admission_function').html(response);
            resolve(); // Resolve the promise
        },
        error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            console.error('Error loading top navbar:', message);
            reject(); // Reject the promise
        },
        complete: function() {
            $('#Loading').html(""); // Hide loading after the request completes, with delay
        }
    });
});
};
            $(document).ready(async function() {

                try {
                    await load_overall_faculty_admission_function();
                    await loadSidebar();
                    await loadTopbar();
                    await loadFooter();
                    await loadBgCard();
                    //await load_faculty_addmission();
                    await load_overall_faculty_addmission();

                } catch (error) {
                    console.error('An error occurred while loading:', error);
                }
            });
    </script>

</body>

</html>