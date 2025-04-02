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
        <div class="col col-4 col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card ">
                <h3>Designation Analysis</h3>
                <div id="faculty-designation-chart"></div>
            </div>
        </div>
        <div class="col col-4 col-lg-6 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Experience Levels</h3>
                <div id="faculty-experience-chart"></div>
            </div>
        </div>
        <div class="col col-4 col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Degree Highlights</h3>
                <div class="card full-width m-1 p-2  flex-container align-center justify-between degree-card-background">
                    <img class="degree-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/doctorates1.svg' ?>" alt="">
                    <p>Doctorates</p>
                    <p class="alert alert-success">500</p>
                </div>
                <div class="card full-width m-1 p-2  flex-container align-center justify-between degree-card-background">
                    <img class="degree-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/philospohers.svg' ?>" alt="">
                    <p>Philosophers</p>
                    <p class="alert alert-success">500</p>
                </div>
                <div class="card full-width m-1 p-2  flex-container align-center justify-between degree-card-background">
                    <img class="degree-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/master degree.svg' ?>" alt="">
                    <p>Masters</p>
                    <p class="alert alert-success">500</p>
                </div>
                <div class="card full-width m-1 p-2  flex-container align-center justify-between degree-card-background">
                    <img class="degree-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/bachelor degree.svg' ?>" alt="">
                    <p>Bachelors</p>
                    <p class="alert alert-success">500</p>
                </div>
                <div class="card full-width m-1 p-2  flex-container align-center justify-between degree-card-background">
                    <img class="degree-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/diploma.svg' ?>" alt="">
                    <p>Diplomas</p>
                    <p class="alert alert-success">500</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {
            try {

                showComponentLoading();

                await faculty_experience_faculty();
                await faculty_designation_chart();


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
