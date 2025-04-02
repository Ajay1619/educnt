<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="main-content-card action-box">
        <h2 class="action-title">Lesson Plan Overview</h2>
        <table id="lesson-plan-table" class="portal-table ">
            <thead>
                <tr>
                    <th>Sl. No</th>
                    <th>Topics</th>
                    <th>Date</th>
                    <th>Slot Timing</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Algebra, Calculus, Geometry</td>
                    <td>2025-02-12</td>
                    <td>10:00 AM - 11:00 AM</td>
                    <td class="action">
                        <button class="lesson-plan-view-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="View" width="24" height="24" class="lesson-plan-list">
                        </button>
                        <button onclick="editLessonPlan()">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" width="24" height="24">
                        </button>
                        <button class="lesson-plan-delete-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg' ?>" alt="Delete" width="24" height="24">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Quantum Mechanics, Thermodynamics</td>
                    <td>2025-02-13</td>
                    <td>11:00 AM - 12:00 PM</td>
                    <td class="action">
                        <button class="lesson-plan-view-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="View" width="24" height="24" class="lesson-plan-list">
                        </button>
                        <button onclick="editLessonPlan()">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" width="24" height="24">
                        </button>
                        <button class="lesson-plan-delete-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg' ?>" alt="Delete" width="24" height="24">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Data Structures, Algorithms</td>
                    <td>2025-02-14</td>
                    <td>02:00 PM - 03:00 PM</td>
                    <td class="action">
                        <button class="lesson-plan-view-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="View" width="24" height="24" class="lesson-plan-list">
                        </button>
                        <button onclick="editLessonPlan()">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" width="24" height="24">
                        </button>
                        <button class="lesson-plan-delete-btn">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg' ?>" alt="Delete" width="24" height="24">
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        (function() {
            window.editLessonPlan = function() {
                const params = {
                    action: 'edit',
                    route: 'faculty'
                };
                const queryString = `?action=${params.action}&route=${params.route}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                console.log('Navigating to:', newUrl);
                window.location.href = newUrl;
            };

            $(document).ready(function() {
                // View button handler
                $('.lesson-plan-view-btn').on('click', async function(e) {
                    e.preventDefault();
                    try {
                        console.log('Eye icon clicked');
                        showComponentLoading();
                        await load_lesson_plan_list_popup();
                        console.log('Popup should be visible now');
                    } catch (error) {
                        const errorMessage = error.message || 'An error occurred while loading the lesson plan popup.';
                        await insert_error_log(errorMessage);
                        await load_error_popup();
                        console.error('Error loading lesson plan popup:', error);
                    } finally {
                        setTimeout(function() {
                            hideComponentLoading();
                        }, 100);
                    }
                });

                // Delete button handler (added)
                $('.lesson-plan-delete-btn').on('click', function(e) {
                    e.preventDefault();
                    // Add your delete logic here
                    console.log('Delete icon clicked');
                    // Example: if (confirm('Are you sure you want to delete this lesson plan?')) {
                    //     // Delete logic
                    // }
                });
            });
        })();
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>