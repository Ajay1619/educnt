<?php
include_once('../../../../config/sparrow.php');

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

    <div class="form-navigation">
        <button class="btn prev-btn">Previous</button>
        <button class="btn next-btn" id="nxt_btn">Next</button>
        <button class="btn submit-btn" id="submit_btn">Submit</button>
    </div>
    <script>
        $(document).ready(function() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const route = urlParams.get('route');
                const action = urlParams.get('action');
                const type = urlParams.get('type');
                let currentUrl = window.location.href;



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
