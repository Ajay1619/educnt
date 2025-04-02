<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    // isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

    <h1>Dashboard</h1>
    <p class="welcome-slogan">Welcome back, Shiyam!</p>
    <div class="row">
        <div class="col col-6 col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <!-- Mentor Students Card -->
            <div class="card">
                <h4>Total Mentor Students</h4>
                <div class="card-content">
                    <h1 id="mentor-students-count">100</h1>
                </div>
            </div>
        </div>
        <div class="col col-6 col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <!-- Recent Achievements Card -->
            <div class="card">
                <h4>Recent Achievements</h4>
                <div class="card-content">
                    <h1 id="recent-achievements-count">120</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-6 col-lg-5 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Achievements Categories</h3>
                <div id="mentor-achievements-chart"></div> <!-- Chart container -->
            </div>
        </div>
        <div class="col col-6 col-lg-6 col-md-7 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Recent Achievements</h3>
                <div class="scrollable-container">
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class=" col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class=" col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class=" col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Student Roles</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl.No</th>
                                <th>Name</th>
                                <th>Year</th>
                                <th>Section</th>
                                <th>Roles</th>
                            </tr>
                        </thead>
                        <tbody id="student-roles-table">
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>3rd Year</td>
                                <td>A</td>
                                <td>Representative</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>2nd Year</td>
                                <td>B</td>
                                <td>Class Leader</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Alex Johnson</td>
                                <td>4th Year</td>
                                <td>C</td>
                                <td>Representative</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <!-- hod view main dashboaard  -->
    <h1>Dashboard</h1>
    <p class="welcome-slogan">Welcome back, HOD Shiyam!</p>

    <div class="row">
        <!-- Faculty Details Card -->
        <div class="col col-6 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <h4>Faculty Details</h4>
                <!-- Pie Chart Section -->
                <div id="faculty-details-chart"></div>
            </div>
        </div>

        <!-- Department Performance Card -->
        <!-- Department Performance Card -->
        <div class="col col-6 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <h4>Department Performance</h4>
                <div class="card-content">
                    <div class="row ">
                        <h2 id="department-performance-value">0%</h2>
                        <span id="performance-indicator">
                            <span id="up-arrow"></span>
                            <span id="down-arrow"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col col-4 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3 id="profile-card">Profile Card</h3>
                <div class="profile_info">
                    <div>
                        <img src="<?= GLOBAL_PATH . '/images/images.jpeg' ?>" alt="Faculty Profile Picture" class="profile-pic" />
                    </div>
                    <div class="faculty-details">
                        <h2 class="faculty-name">Dr. Jane Doe</h2>
                        <p class="faculty-designation">Associate Professor</p>
                        <p class="faculty-department">Department of Computer Science</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h4>Faculty Achievements</h4>
                <div id="faculty-achievements-chart"></div>
            </div>
        </div>
        <div class="col col-4 col-lg-5 col-md-5 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h4>Faculty Roles</h4>
                <div class="scrollable-container roles">
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Class Advisor</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Class Advisor</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Class Advisor</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Recent Achievements</h3>
                <div class="scrollable-container">
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class="col col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class="col col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <div class="col col-4 col-lg-2 col-md-3 col-sm-3 col-xs-3">
                                    <!-- Profile Image -->
                                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                </div>
                                <div class="col col-4 col-lg-8 col-md-7 col-sm-7 col-xs-7">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Student</p>
                                        <p>3rd Year - Section A</p>
                                    </div>
                                </div>
                                <div class="col col-4 col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                    <!-- View Button -->
                                    <button class="btn-success">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Academic Calendar</h3>
                <div id="calendar">
                    <div class="calendar-nav">
                        <button class="prev-month">&#10094;</button>
                        <span class="month-year"></span>
                        <button class="next-month">&#10095;</button>
                    </div>
                    <div id="calendar-body">
                        <!-- Calendar days will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h1>Dashboard</h1>
    <p class="welcome-slogan">Welcome back, principal main Shiyam!</p>

    <div class="row">
        <!-- Faculty Details Card -->
        <div class="col col-6 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <h4>Faculty Details</h4>
                <!-- Pie Chart Section -->
                <div id="faculty-active-details-chart"></div>
            </div>
        </div>
        <div class="col col-6 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <h4>Achievements Percentage</h4>
                <div class="card-content">
                    <div class="row ">
                        <h2 id="achievements-performance-value">0%</h2>
                        <span id="achievements-performance">
                            <span id="up-arrow"></span>
                            <span id="down-arrow"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-3 col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3 id="profile-card">Profile Card</h3>
                <div class="profile_info">
                    <div>
                        <img src="<?= GLOBAL_PATH . '/images/images.jpeg' ?>" alt="Faculty Profile Picture" class="profile-pic" />
                    </div>
                    <div class="faculty-details">
                        <h2 class="faculty-name">Dr. Jane Doe</h2>
                        <p class="faculty-designation">Associate Professor</p>
                        <p class="faculty-department">Department of Computer Science</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3 id="performance-card">Performance Grading</h3>
                <div class="medal-stage">
                    <!-- 2nd Place -->
                    <div class="medal-stage__item second-place">
                        <img src="<?= GLOBAL_PATH . '/images/2nd.png' ?>" alt="2nd Place Medal" class="medal-icon">
                        <h3>ECE Department</h3>
                    </div>

                    <!-- 1st Place -->
                    <div class="medal-stage__item first-place">
                        <img src="<?= GLOBAL_PATH . '/images/1st.png' ?>" alt="1st Place Medal" class="medal-icon">
                        <h3>CSE Department</h3>
                    </div>

                    <!-- 3rd Place -->
                    <div class="medal-stage__item third-place">
                        <img src="<?= GLOBAL_PATH . '/images/3rd.png' ?>" alt="3rd Place Medal" class="medal-icon">
                        <h3>MECH Department</h3>
                    </div>
                </div>

            </div>
        </div>
        <div class="col col-4 col-lg-5 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h4>Faculty Roles</h4>
                <div class="scrollable-container roles">
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Vice Principal</p>
                                        <p>SVCET</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>Dean Of Academics</p>
                                        <p>SVCET</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="section-header-title text-left">
                            <div class="row">
                                <!-- Profile Image -->
                                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="profile-picture">
                                <div class="col col-2 col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                    <!-- Student Details -->
                                    <div class="details">
                                        <h4>John Doe</h4>
                                        <p>HOD</p>
                                        <p>CSE</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Department Achievements</h3>
                <div class="filter-container">
                    <select id="filter-type" class="filter-select">
                        <option value="semester">By Semester</option>
                        <option value="month">By Month</option>
                        <option value="year">By Year</option>
                    </select>
                </div>
                <div id="department-achievements-chart"></div>
            </div>
        </div>
        <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>Academic Calendar</h3>
                <div id="calendar">
                    <div class="calendar-nav">
                        <button class="prev-month">&#10094;</button>
                        <span class="month-year"></span>
                        <button class="next-month">&#10095;</button>
                    </div>
                    <div id="calendar-body">
                        <!-- Calendar days will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- students main dashboard -->
    <h1>Dashboard</h1>
    <p class="welcome-slogan">Welcome back, students main Shiyam!</p>
    <div class="row">
        <div class="col col-3 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3 id="profile-card">Profile Card</h3>
                <div class="profile_info">
                    <div>
                        <img src="<?= GLOBAL_PATH . '/images/images.jpeg' ?>" alt="Faculty Profile Picture" class="profile-pic" />
                    </div>
                    <div class="faculty-details">
                        <h2 class="faculty-name">Dr. Jane Doe</h2>
                        <p class="faculty-designation">Associate Professor</p>
                        <p class="faculty-department">Department of Computer Science</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-9 col-lg-7 col-md-7 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h4>Achievements of Students</h4>
                <div id="student-achievements-chart"></div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col col-9 col-lg-5 col-md-5 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h4>Achievements of Students</h4>
                <div id="student-achievements-pie-chart"></div>
            </div>

        </div>
    </div>
    </div>


    <script src="<?= MODULES . '/faculty_profile/js/faculty_profile_dashboards.js' ?>"></script>

    <script>
        $(document).ready(async function() {
            try {
                await summary_mentor_card_faculty();
                await summary_principal_card_faculty();
                await achievements_pie_chart();

                await hod_summary_card();
                await hod_achievements_category_card();
                await principal_summary_card();
                await principal_Achievements_Chart();
                await studentAchievementsChart();
                await student_Achievements_PieChart();
                //await initSubjectChart();
                // await initFacultyRoleSlider();
                // await initCalendar();


            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
