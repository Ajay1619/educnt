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
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

    <!-- Confirmation Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Confirm Action
            </div>

            <!-- Popup Content -->
            <div class="popup-content">
                <p class="popup-quotes">"Are You Certain You Wish To Reset The Mentorship?"</p>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <div class="popup-action-buttons">
                    <button class="btn-success mentor-rest-confirm-btn">Yes, Reset Mentorship</button>
                    <button class="btn-error mentor-rest-cancel-btn">No, Keep Mentorship</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        //document.ready function
        $(document).ready(async function() {

            $('.mentor-rest-confirm-btn').on('click', async function() {
                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action');
                const route = urlParams.get('route');
                const type = urlParams.get('type');
                const tab = 'reset';

                const params = {
                    action: action,
                    route: route,
                    type: type,
                    tab: tab
                };

                // Construct the new URL with query parameters
                const queryString = `?action=${action}&route=${route}&type=${type}&tab=${tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                // Use pushState to set the new URL and pass params as the state object
                window.history.pushState(params, '', newUrl);
                load_faculty_dept_reset_form_popup()
            });
        });
        <?php
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }
        ?>