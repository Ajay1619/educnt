<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="main-content-card action-box">
        <div class="main-content-card-header">
            <h2 class="action-title">Department Fee Overview</h2>
        </div>
        <div id="fee-table-container">
            <table class="portal-table" id="student-fee-table">
                <thead>
                    <tr>
                        <th>Sl.No</th>
                        <th>Student Name</th>
                        <th>Register Number</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>150000</td>
                        <td><span class="alert alert-success">Structured</span></td>
                        <td>
                            <img src="<?= GLOBAL_PATH ?>/images/svgs/eye.svg" alt="View" class="fees-individual-report" data-student-id="1" onclick="viewStudentFees(1)">
                            <img src="<?= GLOBAL_PATH ?>/images/svgs/sidenavbar_icons/old_icons/edit.svg" alt="Edit" class="action-icon individual-student-fees-edit" data-student-id="1" onclick="editStudentFees(1)">
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Doe</td>
                        <td>250000</td>
                        <td><span class="alert alert-error">Not Structured</span></td>
                        <td>
                            <img src="<?= GLOBAL_PATH ?>/images/svgs/eye.svg" alt="View" class="action-icon individual-student-fees-popup" data-student-id="2" onclick="viewStudentFees(2)">
                            <img src="<?= GLOBAL_PATH ?>/images/svgs/sidenavbar_icons/old_icons/edit.svg" alt="Edit" class="action-icon individual-student-fees-edit" data-student-id="2" onclick="editStudentFees(2)">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // View function with URL manipulation
        const viewStudentFees = (studentId) => {
            const params = {
                action: 'view',
                route: 'faculty',
                type: 'individual-fees',
                id: studentId
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&id=${params.id}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;

            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);

            // Load the content dynamically
            loadStudentFeesContent(studentId);
        };

        // Function to load student fees content dynamically
        async function loadStudentFeesContent(studentId) {
            const pathData = {
                action: 'view',
                route: 'faculty',
                type: 'individual-fees'
            };
            try {
                showComponentLoading();
                await load_individual_student_fees_view_table(studentId, pathData);
            } catch (error) {
                const errorMessage = error.message || 'Error loading student fees';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error(errorMessage, error);
            } finally {
                setTimeout(hideComponentLoading, 100);
            }
        }

        // Edit function with URL manipulation
        const editStudentFees = (studentId) => {
            const params = {
                action: 'edit',
                route: 'faculty',
                id: studentId
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&id=${params.id}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;

            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);

            // Load the edit form dynamically
            loadStudentEditFeesContent(studentId);
        };

        // Function to load student fees edit form dynamically
        async function loadStudentEditFeesContent(studentId) {
            const pathData = {
                action: 'edit',
                route: 'faculty',
            };
            try {
                showComponentLoading();
                await load_student_admission_fees_edit(studentId, pathData);
            } catch (error) {
                const errorMessage = error.message || 'Error loading student fees edit form';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error(errorMessage, error);
            } finally {
                setTimeout(hideComponentLoading, 100);
            }
        }

        // Handle browser back/forward button navigation
        window.onpopstate = function(event) {
            if (event.state && event.state.id) {
                const action = event.state.action;
                const studentId = event.state.id;

                if (action === 'view') {
                    loadStudentFeesContent(studentId);
                } else if (action === 'edit') {
                    loadStudentEditFeesContent(studentId);
                }
            }
        };
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>