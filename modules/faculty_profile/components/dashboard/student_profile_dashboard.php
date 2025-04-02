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
    <!-- principal view profile dashboard -->

    <h2>Student Profile Dashboard</h2>
    <p class="welcome-slogan text-light"></p>
    <div class="row">
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Mentor Students Card -->
            <div class="card full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="total-learners-count">30</h1>
                        <h6 class="text-light">Total Learners</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/total students count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Recent Achievements Card -->
            <div class="card  full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="male-learners-count">30</h1>
                        <h6 class="text-light">Male Learners</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/boys student count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Mentor Students Card -->
            <div class="card full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="female-learners-count">30</h1>
                        <h6 class="text-light">Female Learners</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/girls students count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Recent Achievements Card -->
            <div class="card  full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="drop-outs-count">30</h1>
                        <h6 class="text-light">Drop Outs</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/drop out count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <!-- Recent Achievements Card -->
            <div class="card  full-width">
                <div class="row">
                    <div class="col-9">
                        <h1 id="mentees-count">30</h1>
                        <h6 class="text-light">Your Mentees</h6>
                    </div>
                    <div class="col-3">
                        <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/dashboard icons/mentees count.svg' ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Student Strength Across Departments</h3>
                <div id="faculty-strength-chart"></div>
            </div>
        </div>
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Achievements Over the Years</h3>
                <div id="faculty-dept-achievements-history"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-4 col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card ">
                <h3>Committees Analysis</h3>
                <div id="student-committees-chart"></div>
            </div>
        </div>
        <div class="col col-4 col-lg-6 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Activities Over Departments</h3>
                <div id="student-activities-dept-chart"></div>
            </div>
        </div>
        <div class="col col-4 col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Activity Champs</h3>
                <div class="card student-card m-1 p-2 flex-container align-center justify-between student-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Mr. John Doe</p>
                    <p class="alert alert-info">CSE</p>
                </div>
                <div class="card student-card m-1 p-2 flex-container align-center justify-between student-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Jane Smith</p>
                    <p class="alert alert-info">MECH</p>
                </div>
                <div class="card student-card m-1 p-2 flex-container align-center justify-between student-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>David Lee</p>
                    <p class="alert alert-info">EEE</p>
                </div>
                <div class="card student-card m-1 p-2 flex-container align-center justify-between student-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Sarah Johnson</p>
                    <p class="alert alert-info">ECE</p>
                </div>
                <div class="card student-card m-1 p-2 flex-container align-center justify-between student-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Mark Williams</p>
                    <p class="alert alert-info">BME</p>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Research Champs</h3>
                <div class="card student-research-card m-1 p-2 flex-container align-center justify-between student-research-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Mr. John Doe</p>
                    <p class="alert alert-info">CSE</p>
                </div>
                <div class="card student-research-card m-1 p-2 flex-container align-center justify-between student-research-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Jane Smith</p>
                    <p class="alert alert-info">MECH</p>
                </div>
                <div class="card student-research-card m-1 p-2 flex-container align-center justify-between student-research-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>David Lee</p>
                    <p class="alert alert-info">EEE</p>
                </div>
                <div class="card student-research-card m-1 p-2 flex-container align-center justify-between student-research-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Sarah Johnson</p>
                    <p class="alert alert-info">ECE</p>
                </div>
                <div class="card student-research-card m-1 p-2 flex-container align-center justify-between student-research-card-background">
                    <img class="student-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Mark Williams</p>
                    <p class="alert alert-info">BME</p>
                </div>
            </div>

        </div>
        <div class="col col-8 col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <div class="dashboard-table-container popup-card">
                <h3>Recent Achievements</h3>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Department</th>
                            <th>Achievement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mr. John Doe</td>
                            <td>CSE</td>
                            <td>Best Research Paper Award</td>
                        </tr>
                        <tr>
                            <td>Sarah Williams</td>
                            <td>MECH</td>
                            <td>Employee of the Year</td>
                        </tr>
                        <tr>
                            <td>David Brown</td>
                            <td>EEE</td>
                            <td>Best Leadership in Project</td>
                        </tr>
                        <tr>
                            <td>Emily Johnson</td>
                            <td>ECE</td>
                            <td>Top Sales Performer</td>
                        </tr>
                        <tr>
                            <td>Michael Smith</td>
                            <td>BME</td>
                            <td>Innovation Award</td>
                        </tr>
                        <tr>
                            <td>Michael Smith</td>
                            <td>BME</td>
                            <td>Innovation Award</td>
                        </tr>
                        <tr>
                            <td>Michael Smith</td>
                            <td>BME</td>
                            <td>Innovation Award</td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="<?= MODULES . '/faculty_profile/js/student_profile_dashboards.js' ?>"></script>
    <script>
        $(document).ready(async function() {
            const studentSlogans = [
                "Empowering education, one student profile at a time.", /* General */
                "Where academic journeys are visualized.", /* General */
                "A window into every student's potential.", /* Principal */
                "Shaping the future with every profile viewed.", /* HOD */
                "Unlocking student success, one profile at a time.", /* Faculty */
                "Every student story, told through data.", /* General */
                "Transforming learning through detailed profiles.", /* Principal */
                "Where educators meet excellence.", /* Teaching Faculty */
                "A profile for every future leader.", /* Principal */
                "Leading education with insight into every profile." /* HOD */
            ];


            const generate_profile_slogan = () => {
                const randomSlogan = studentSlogans[Math.floor(Math.random() * studentSlogans.length)];
                $(".welcome-slogan").text('"' + randomSlogan + '"');
            }
            try {

                showComponentLoading();
                await generate_profile_slogan();
                await student_dept_count();
                await faculty_dept_achievements_history();
                await student_activities_dept_faculty();
                await student_committees_chart();


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
