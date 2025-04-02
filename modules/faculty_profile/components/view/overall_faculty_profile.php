<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <section id="bg-card"></section>
    <section id="overall-profile-table"></section>
    <section id="overall"></section>

    <script>
        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/view/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token

                    },
                    success: function(response) {
                        $('#bg-card').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };




        $(document).ready(async function() {
            try {
                await loadBgCard();
                await callAction();
                await loadBreadcrumbs()
                // await viewAchievements();
                await faculty_overall_profile_table();
                //await tableAchievements();
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>