<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>
    <!-- view profile dashboard -->

    <h2>Faculty Profile Dashboard</h2>
    <p class="welcome-slogan text-light"></p>
    <div id="statisitcs-card"></div>
    <div id="top-row"></div>
    <div id="mid-row"></div>
    <div id="bottom-row"></div>
    <div id="profile-dashboard-functions"></div>


    <script src="<?= MODULES . '/faculty_profile/js/faculty_profile_dashboards.js' ?>"></script>
    <script>
        $(document).ready(async function() {
            const load_dashboard_functions = () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_profile/functions/faculty_profile_dashboard_functions.php', ENT_QUOTES, 'UTF-8') ?>',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: function(response) {
                            $('#profile-dashboard-functions').html(response);
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

            const slogans = [
                "Profiles: The final frontier." /* (Star Trek) */ ,
                "Every story has a profile. This is where they begin." /* (Once Upon a Time) */ ,
                "The Fellowship of the Profiles is ready." /* (The Lord of the Rings) */ ,
                "Unlock the secrets—one profile at a time." /* (National Treasure) */ ,
                "Profiles are coming." /* (Game of Thrones) */ ,
                "To infinity and beyond... of faculty management!" /* (Toy Story) */ ,
                "Profiles so detailed, it’s elementary, my dear Watson." /* (Sherlock Holmes) */ ,
                "The one profile to rule them all." /* (The Lord of the Rings) */ ,
                "Suit up! The profiles await." /* (How I Met Your Mother) */ ,
                "Profiles: The good, the brilliant, and the unforgettable." /* (The Good, the Bad, and the Ugly) */
            ];

            const generate_profile_slogan = () => {
                const randomSlogan = slogans[Math.floor(Math.random() * slogans.length)];
                $(".welcome-slogan").text('"' + randomSlogan + '"');
            }
            try {

                showComponentLoading();
                await generate_profile_slogan();
                await load_dashboard_functions();
                await load_faculty_profile_statistics_card_dashboard();
                await load_faculty_top_row_dashboard();
                await load_faculty_mid_row_dashboard();
                await load_faculty_bottom_row_dashboard();

            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 1000)
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
