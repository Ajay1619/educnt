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
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Mentor Students Card -->
            <div class="card full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="authorities-count"></h1>
                        <h6 class="text-light">Authorities</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/authority count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Recent Achievements Card -->
            <div class="card  full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="class-advisors-count"></h1>
                        <h6 class="text-light">Class Advisors</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/class advisors count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Mentor Students Card -->
            <div class="card full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="teaching-faculty-count"></h1>
                        <h6 class="text-light">Teaching Faculty</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/teaching faculty count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Recent Achievements Card -->
            <div class="card  full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="non-teaching-faculty-count"></h1>
                        <h6 class="text-light">Non Teaching Faculty</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/non teaching faculty count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {

                showComponentLoading();
                await fetch_faculty_profile_statistics_card_dashboard();


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
