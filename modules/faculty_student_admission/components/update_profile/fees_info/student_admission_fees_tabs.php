<?php
include_once('../../../../../config/sparrow.php');

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

    <div class="tab-nav">
        <button class="tab-btn fees" data-tab="0">Fees Structure</button>
        <button class="tab-btn concession" data-tab="1">Concession Details</button>
    </div>
    </div>
    <section id="update_personal_profile">
        <div class="step-content active" data-step="1">
            <section id="info"></section>

        </div>
    </section>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <script>
        $(document).ready(async function() {
            try {
                await loadfeesComponentsBasedOnURL();

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
