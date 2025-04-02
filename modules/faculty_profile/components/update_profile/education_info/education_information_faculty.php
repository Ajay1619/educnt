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
        <button class="tab-btn schools" data-tab="0">Schoolings</button>
        <button class="tab-btn degrees" data-tab="1">Degrees</button>
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
                await load_update_education_profile_components();

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
