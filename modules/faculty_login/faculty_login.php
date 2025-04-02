<?php
require_once('../../config/sparrow.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= GLOBAL_PATH . '/images/app_logo.jpg' ?>">
    <title>Login Module</title>
    <link rel="stylesheet" href="<?= GLOBAL_PATH  . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_login/css/login_page.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_login/css/forget_page.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_login/css/otp.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES  . '/faculty_login/css/reset_password.css' ?>" />
</head>

<body>
    <div id="toast-container"></div>
    <img src="<?= GLOBAL_PATH . '/images/svgs/Formula-pana.svg' ?>" class="svg-image-top-left " alt="Top Left SVG">
    <img src="<?= GLOBAL_PATH . '/images/svgs/Seminar-amico.svg' ?>" class="svg-image-bottom-right" alt="Bottom Right SVG">
    <section id="login_card"></section> <!-- This will dynamically load the forms -->


    <!-- Load jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>
    <script>
        const load_login_form = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_login/components/login.php' ?>',
                success: function(response) {
                    $('#login_card').html(response); // Load the login form
                },
                error: function(xhr, status, error) {
                    console.error('Error loading login form:', error);
                }
            });
        };

        // AJAX functions to load different forms
        const load_forgot_password_form = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_login/components/forgot_password.php' ?>',
                success: function(response) {
                    $('#login_card').html(response); // Load forgot password form
                },
                error: function(xhr, status, error) {
                    console.error('Error loading forgot password form:', error);
                }
            });
        };



        // Load OTP form
        const load_otp_form = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_login/components/otp_password.php' ?>',
                success: function(response) {
                    $('#login_card').html(response); // Load OTP form
                },
                error: function(xhr, status, error) {
                    console.error('Error loading OTP form:', error);
                }
            });
        };

        // Load Reset Password form
        const load_reset_Password_form = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_login/components/password_reset.php' ?>',
                success: function(response) {
                    $('#login_card').html(response); // Load Reset Password form
                },
                error: function(xhr, status, error) {
                    console.error('Error loading reset password form:', error);
                }
            });
        };

        <?php if (!isset($_GET['action'])) { ?>
            load_login_form();
        <?php } elseif (isset($_GET['action']) && $_GET['action'] == 'forgot_password') { ?>
            load_forgot_password_form();
        <?php } elseif (isset($_GET['action']) && $_GET['action'] == 'send_otp') { ?>
            load_otp_form();
        <?php } elseif (isset($_GET['action']) && $_GET['action'] == 'reset_password') { ?>
            load_reset_Password_form();
        <?php } ?>
    </script>
</body>

</html>