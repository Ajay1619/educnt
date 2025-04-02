<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="tabs mt-6">
        <div class="tab active" id="fine-charge-tab">Charge Fine</div>
        <div class="tab" id="log-book-tab">Log Book</div>
    </div>
    <div class="main-content-card action-box" id="fine-contents">
        <h2 class="action-title">Issue Fine</h2>
        <div id="charge-fine-contents">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/fine_action_image.svg' ?>" alt="">
            <p class="action-text">
                "You're about to issue a fine to students. Do you want to fine <span class="highlight" id="single-fine-form">a single student</span> or <span class="highlight" id="bulk-fine-form">upload in bulk</span>?"
            </p>
            <div class="action-hint">
                *Discipline isn't punishment—it's guidance, just like Professor X guiding the X-Men.*
            </div>
        </div>

    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.tab').removeClass('active');


                const urlParams = new URLSearchParams(window.location.search);
                const route = urlParams.get('route');
                const tab = urlParams.get('tab');
                switch (route) {
                    case "faculty":
                        switch (tab) {
                            case "charge":

                                $('#fine-charge-tab').addClass('active');
                                break;
                            case "charge-single":
                                await load_student_charge_single_fine();

                                $('#fine-charge-tab').addClass('active');
                                break;
                            case "charge-bulk":
                                await load_student_charge_bulk_fine();

                                $('#fine-charge-tab').addClass('active');
                                break;
                            case "log-book":

                                await load_student_fine_log_book();

                                $('#log-book-tab').addClass('active');
                                break;
                            default:
                                window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                                break;
                        }
                        break;

                    default:
                        window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                        break;
                }

                await tabs_active();
                // Optional functionality
                $('#fine-charge-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'fine',
                        tab: 'charge'
                    });
                    await load_student_fine();
                });
                $('#log-book-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'fine',
                        tab: 'log-book'
                    });
                    await load_student_fine_log_book();
                });


                $('#single-fine-form').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'fine',
                        tab: 'charge-single'
                    });
                    await load_student_charge_single_fine();
                });
                $('#bulk-fine-form').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'fine',
                        tab: 'charge-bulk'
                    });
                    await load_student_charge_bulk_fine();
                });
            } catch (error) {
                // Get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>