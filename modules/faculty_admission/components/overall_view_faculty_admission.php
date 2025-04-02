<?php 
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
?>

    <div class="main-content-card">
        <div class="content">
            <table id="admission-faculty-Table">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggle(this)"></th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>View Port</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox" name="profileCheckbox"></td>
                        <td>
                            <div class="row align-items-center">
                                <div class="col">
                                    <img src="<?= GLOBAL_PATH . '/images/avatar.png' ?>" class="profile_image" alt="Profile Image" width="40" height="40">
                                </div>
                                <div class="col">
                                    <div>John Doe</div>
                                    <div class="designation">Professor</div> <!-- Add designation here -->
                                </div>
                            </div>
                        </td>
                        <td>+1 234 567 890</td>
                        <td class="status-icons">
                            <a href="#">
                                <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/user-tick.svg' ?>" alt="Accept" width="24" height="24">
                            </a>
                            <a href="#">
                                <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/user-remove.svg' ?>" alt="Decline" width="24" height="24">
                            </a>
                        </td>
                        <td class="actions">
                            <a href="view_profile.html">
                                <img src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="View" width="24" height="24">
                            </a>
                            <a href="edit_profile.html">
                                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" width="24" height="24">
                            </a>
                            <a href="delete_profile.html">
                                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/delete.svg' ?>" alt="Delete" width="24" height="24">
                            </a>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $('#admission-faculty-Table').DataTable({
            scrollX: true,
            initComplete: function(settings, json) {
                $('.dt-layout-table .dt-layout-cell').css('width', '100%');
                $('.dt-scroll-headInner').css('width', '100%');
                $('.dataTable').css('width', '100%');
            }
        });

        function toggle(source) {
            checkboxes = document.getElementsByName('profileCheckbox');
            for(var i=0, n=checkboxes.length; i<n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
    <style>
        
        
        /* Center align table header and data */
        th, td {
            text-align: center;
        }
        /* Make sure icons and table content are aligned properly */
        td img {
            vertical-align: middle;
        }
    </style>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
