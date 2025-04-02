<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

?>
    <div class="row" id="wishes">
        <div class="col col-6 col-xs-12 text-left">
            <h2>Personal Dashboard</h2>
        </div>
        <div class="col col-6 col-xs-12 text-right">
            <h2 id="dashboard-wishes"></h2>
            <p class="dashboard-wishes-slogans text-light"></p>
        </div>
    </div>

    <div class="row" id="profile-details">
        <div class="col col-4">
            <div class="popup-card">
                <div class="text-center">
                    <img class="profile-pic"
                        src="<?= empty($logged_profile_pic)
                                    ? GLOBAL_PATH . '/images/profile pic placeholder.png'
                                    : GLOBAL_PATH . '/uploads/faculty_profile_pic/' . $logged_profile_pic ?>"
                        alt="Profile Picture">

                    <h4 id="profile-name"><?= $logged_faculty_salutation . ' ' . $logged_first_name . ' ' . $logged_middle_name . ' ' . $logged_last_name . ' ' . $logged_initial . '' ?></h4>
                    <h6><?= $logged_designation ?></h6>
                    <h6 class="text-light"><?= $logged_dept_title ?></h6>

                </div>
            </div>
        </div>
        <div class="col col-4">
            <div class="row">
                <div class="card full-width">
                    <div class="row">
                        <div class="col-9">
                            <h1 id="faculties-count">30</h1>
                            <h6 class="text-light">Total Faculties</h6>
                        </div>
                        <div class="col-3">
                            <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/total faculties.svg' ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card full-width">
                    <div class="row">
                        <div class="col-9">
                            <h1 id="students-count">30</h1>
                            <h6 class="text-light">Total Students</h6>
                        </div>
                        <div class="col-3">
                            <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/total students.svg' ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-4">
            <div class="popup-card text-center">
                <div id="performance-chart"></div>
                <h4>OverAll Performance</h4>
                <p id="overallperformance-quotes" class="text-light"></p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {
            try {
                showComponentLoading()
                await load_dashboard_wishes('<?= $logged_first_name ?>');
                await load_performance_chart();
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
                }, 100)
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
