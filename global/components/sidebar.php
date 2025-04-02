<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>

    <div class="sidebar">
        <div class="logo-container">
            <a href="<?= BASEPATH . '/dashboard' ?>">
                <img id="sidebar-logo" alt="Logo" class="logo">
            </a>
        </div>
        <ul class="nav-icons">
            <?php if (strtolower($routing) == 'student' ||  $logged_role_id == 1 ||  $logged_role_id == 2 || $logged_role_id == 3 || $logged_role_id == 6 ||  $logged_role_id == 7 ||  $logged_role_id == 4 ||  $logged_role_id == 5) { ?>
                <a href="<?= BASEPATH . '/faculty-profile?action=view&route=' . strtolower($routing) ?>&type=overall">
                    <li class="nav-list tooltip tooltip-right" id="profile">
                        <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/profile-circle.svg' ?>" alt="Profile">
                        <span class="tooltip-text">
                            <strong>Profile</strong>
                            <div>Manage <?= $routing == 'Faculty' ? 'Your Faculty\'s' : 'Student\'s' ?> profile settings</div>
                        </span>
                    </li>
                </a>
            <?php } ?>
            <a href="<?= BASEPATH . '/faculty-roles-responsibilities?action=view&route=' . strtolower($routing) . '&type=authorities' ?>">
                <li class="nav-list tooltip tooltip-right" id="roles">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/r&r.svg' ?>" alt="R&R">
                    <span class="tooltip-text">
                        <strong>Roles</strong>
                        <div>View <?= $routing == 'Faculty' ? 'Your' : 'Student\'s' ?> Roles</div>
                    </span>
                </li>
            </a>
            <?php
            if (strtolower($routing) == 'student') {
            } else {
            ?>
                <a href="<?= BASEPATH . '/faculty-achievements?action=view&route=' . strtolower($routing) ?>">

                    <li class="nav-list tooltip tooltip-right" id="achievements">
                        <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/achivements.svg' ?>" alt="Achievements">
                        <span class="tooltip-text">
                            <strong>Achievements</strong>
                            <div>Check <?= $routing == 'Faculty' ? 'Your' : 'Student\'s' ?> achievements</div>
                        </span>
                    </li>
                </a>
            <?php } ?>
            <?php if (strtolower($routing) == 'student') { ?>

            <?php } else if (strtolower($routing) == 'faculty' && in_array($logged_role_id, $main_roles) || $logged_role_id == 13) { ?>
                <a href="<?= BASEPATH . '/faculty-student-admission?action=view&route=' . strtolower($routing) . '&type=overall' ?>">
                    <li class="nav-list tooltip tooltip-right" id="admission">
                        <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/admission.svg' ?>" alt="Admission">
                        <span class="tooltip-text">
                            <strong>Admission</strong>
                            <div>Information about admissions</div>
                        </span>
                    </li>
                </a>
            <?php } ?>

            <?php if ($logged_role_id != 0) { ?>
                <a href="<?= BASEPATH . '/faculty-class-schedule?action=view&route=' . strtolower($routing) ?>">
                    <li class="nav-list tooltip tooltip-right" id="class-schedule">
                        <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/schedule.svg' ?>" alt="Class Schedule">
                        <span class="tooltip-text">
                            <strong>Class Schedule</strong>
                            <div>View your daily class timings.</div>
                        </span>
                    </li>
                </a>
            <?php } ?>
            <?php
            if (strtolower($routing) == 'student') {
            } else {
            ?>
                <a href="<?= BASEPATH . '/faculty-student-attendance?action=view&route=' . strtolower($routing) ?>">
                    <li class="nav-list tooltip tooltip-right">
                        <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/attendance.svg' ?>" alt="Attendance">
                        <span class="tooltip-text">
                            <strong>Attendance</strong>
                            <div>Track <?= $routing == 'Faculty' ? 'Your' : 'Student\'s' ?> attendance records</div>
                        </span>
                    </li>
                </a>

            <?php } ?>

            <!-- <a href="<?= BASEPATH . '/material' ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/material.svg' ?>" alt="Material">
                    <span class="tooltip-text">
                        <strong>Material</strong>
                        <div>Access study materials</div>
                    </span>
                </li>
            </a>

            <a href="<?= BASEPATH . '/exam' ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/exam.svg' ?>" alt="Exam">
                    <span class="tooltip-text">
                        <strong>Exam</strong>
                        <span>Manage <?= $routing == 'Faculty' ? 'Your' : 'Student\'s' ?> exams</span>
                    </span>
                </li>
            </a>  -->

            <a href="<?= BASEPATH . '/faculty-stock-inventory?action=view&route=' . strtolower($routing) ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidebar/stocks.svg' ?>" alt="Stocks">
                    <span class="tooltip-text">
                        <strong>Stocks</strong>
                        <div>Manage stock materials</div>
                    </span>
                </li>
            </a>
            <a href="<?= BASEPATH . '/faculty-student-examination?action=view&route=' . strtolower($routing) ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/new_sidenavbar_icons/exam.svg' ?>" alt="Stocks">
                    <span class="tooltip-text">
                        <strong>Examination</strong>
                        <div>Manage Examination Records</div>
                    </span>
                </li>
            </a>
            <a href="<?= BASEPATH . '/faculty-student-fees?action=view&type=overall&route=' . strtolower($routing) ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/new_sidenavbar_icons/coin.svg' ?>" alt="Stocks">
                    <span class="tooltip-text">
                        <strong>Fees</strong>
                        <div>Manage Fees Records</div>
                    </span>
                </li>
            </a>
            <a href="<?= BASEPATH . '/faculty-lesson-plan?action=view&route=' . strtolower($routing) ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/new_sidenavbar_icons/strategy-plan.svg' ?>" alt="Stocks">
                    <span class="tooltip-text">
                        <strong>Lesson Plan</strong>
                        <div>Manage Lesson Plan</div>
                    </span>
                </li>
            </a>
            <a href="<?= BASEPATH . '/faculty-student-placement?action=view&route=' . strtolower($routing) ?>">
                <li class="nav-list tooltip tooltip-right">
                    <img class="nav-icon" src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/new_sidenavbar_icons/internship.svg' ?>" alt="Stocks">
                    <span class="tooltip-text">
                        <strong>Placement</strong>
                        <div>Manage Placement Details</div>
                    </span>
                </li>
            </a>

        </ul>
    </div>

    <script>
        const fetch_sidebar_logo = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_sidebar_logo.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        const sidebar_logo = response.data.institution_logo;
                        if (sidebar_logo) {
                            $('#sidebar-logo').attr('src', '<?= DEV_GLOBAL_PATH . '/uploads/logo/' ?>' + sidebar_logo);
                        } else {
                            $('#sidebar-logo').attr('src', '<?= GLOBAL_PATH . '/images/app_logo.png' ?>');
                        }

                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }

        const sidebar_active = () => {
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            if (lastPath == 'faculty-profile') {

                $('#profile').addClass('active');
                $('#roles').removeClass('active');
                $('#achievements').removeClass('active');
                $('#admission').removeClass('active');

            } else if (lastPath == 'faculty-roles-responsibilities') {

                $('#profile').removeClass('active');
                $('#roles').addClass('active');
                $('#achievements').removeClass('active');
                $('#admission').removeClass('active');

            } else if (lastPath == 'faculty-achievements') {

                $('#profile').removeClass('active');
                $('#roles').removeClass('active');
                $('#achievements').addClass('active');
                $('#admission').removeClass('active');

            } else if (lastPath == 'faculty-student-admission') {

                $('#profile').removeClass('active');
                $('#roles').removeClass('active');
                $('#achievements').removeClass('active');
                $('#admission').addClass('active');


            } else if (lastPath == 'faculty-class-schedule') {

                $('#profile').removeClass('active');
                $('#roles').removeClass('active');
                $('#achievements').removeClass('active');
                $('#admission').removeClass('active');
                $('#class-schedule').addClass('active');

            }
        }
        //document ready function
        $(document).ready(async function() {
            try {
                await fetch_sidebar_logo();
                await sidebar_active();
            } catch (error) {
                // Display an error message in an element or console
                console.error("Error fetching sidebar logo:", error);
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>