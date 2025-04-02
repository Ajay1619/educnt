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
        <button class="tab-btn personal" data-tab="0">Personal Details</button>
        <button class="tab-btn contact" data-tab="1">Contact Details</button>
        <button class="tab-btn address" data-tab="2">Address Details</button>
        <button class="tab-btn official" data-tab="2">Official Details</button>
    </div>
    <section id="update_personal_profile">

        <div class="step-content active" data-step="0">
            <section id="info"></section>
        </div>
    </section>
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <script>
        $(document).ready(async function() {
            try {

                load_personal_info_components();



            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
