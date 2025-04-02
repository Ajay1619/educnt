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

    <section id="edit-request">
        <div class="container">
            <div class="action-box">
                <div class="action-title">The Mentor Chronicles</div>
                <p class="action-text">
                    "You are authorized to <span id="mentor-reset-action" class="highlight">RESET</span> the department's faculty assignments or <span id="mentor-reset-swap" class="highlight">SWAP</span> students between faculty."
                </p>

                <div class="action-hint">
                    *Mentorship, like Iron Man's to Spider-Man, is a legacy of responsibility and leadership.*
                </div>
            </div>
        </div>
    </section>
    <section id="mentor-faculty-reset"></section>
    <section id="mentor-faculty-mentor-list">
        <div class="row">
            <div class="col col-5 col-lg-5 col-md-12 col-sm-12 col-xs-12 mentor-faculty-card">
            </div>
            <div class="col col-7 col-lg-7 col-md-12 col-sm-12 col-xs-12 mentor-assignment-panel">

            </div>

        </div>

    </section>
    <section id="mentor-faculty-popup"></section>
    <section id="mentor-faculty-student-list"></section>

    <script>
        //document.ready function
        $(document).ready(async function() {

            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const route = urlParams.get('route');
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');

            if (action == 'edit' && route == 'faculty' && type == 'mentors' && tab == 'reset') {
                await load_faculty_dept_reset_form_popup()
                $('#mentor-faculty-popup').html("");
            }
            $('#mentor-reset-action').on('click', async function() {
                await load_mentor_faculty_reset_confirmation_popup()
            })
            $('#mentor-reset-swap').on('click', async function() {
                await load_mentor_faculty_swap_popup()
            })


        });
        <?php
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }
        ?>