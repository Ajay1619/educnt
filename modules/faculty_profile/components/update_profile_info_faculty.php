<?php require_once('../../../config/sparrow.php'); ?>


<section id="update_faculty_profile">
    <div class="staff_admission_container">
        <section id="stepper"></section>
        <section id="content"></section>
    </div>

    <div id="faculty-update-profile-functions"></div>
    <div id="faculty-update-education-functions"></div>
    <div id="faculty-update-experience-functions"></div>
</section>

<script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

<script>
    const faculty_update_profile_functions = () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_profile/functions/faculty_update_profile_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                },
                success: function(response) {
                    $('#faculty-update-profile-functions').html(response);
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

    const faculty_update_education_functions = () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_profile/functions/faculty_update_education_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                },
                success: function(response) {
                    $('#faculty-update-education-functions').html(response);
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

    const faculty_update_experience_functions = () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_profile/functions/faculty_update_experience_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                },
                success: function(response) {
                    $('#faculty-update-experience-functions').html(response);
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
            await faculty_update_profile_functions();
            await faculty_update_education_functions();
            await faculty_update_experience_functions();
            await StepperProfileForm();
            await load_update_profile_components();



        } catch (error) {
            console.error('An error occurred while loading:', error);
        }
    });
</script>