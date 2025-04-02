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
    <div class="row">
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3><?= ($logged_role_id != 6) ? "Faculty Distribution Across Departments" : "Committee Activity Breakdown" ?></h3>
                <div id="faculty-strength-chart">
                    <div id="no-data-available">
                        <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/no-data.gif' ?>" alt="">
                        <p class="text-light">"Why so serious? It's just no data available... for now."</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Achievements Over the Years</h3>
                <div id="faculty-dept-achievements-history">
                    <div id="no-data-available">
                        <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/no-data.gif' ?>" alt="">
                        <p class="text-light">"The line chart snapped... No data survived."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {
            try {

                showComponentLoading();

                await fetch_faculty_profile_top_row_dashboard();
                await faculty_dept_count();
                await faculty_dept_achievements_history();


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
