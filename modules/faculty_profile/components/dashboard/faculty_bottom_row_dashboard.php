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
        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="popup-card">
                <h3>New Team Members</h3>
                <div class="card employee-card m-1 p-2 flex-container align-center justify-between employee-card-background">
                    <img class="employee-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Mr. John Doe</p>
                    <p class="alert alert-info">CSE</p>
                </div>
                <div class="card employee-card m-1 p-2 flex-container align-center justify-between employee-card-background">
                    <img class="employee-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Jane Smith</p>
                    <p class="alert alert-info">MECH</p>
                </div>
                <div class="card employee-card m-1 p-2 flex-container align-center justify-between employee-card-background">
                    <img class="employee-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>David Lee</p>
                    <p class="alert alert-info">EEE</p>
                </div>
                <div class="card employee-card m-1 p-2 flex-container align-center justify-between employee-card-background">
                    <img class="employee-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
                    <p>Sarah Johnson</p>
                    <p class="alert alert-info">ECE</p>
                </div>
                <div class="card employee-card m-1 p-2 flex-container align-center justify-between employee-card-background">
                    <img class="employee-photo" src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Employee Photo">
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
                            <th>Employee Name</th>
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

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
