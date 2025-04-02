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
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>
    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-add-button" id="faculty-class-schedule-add-button">Add</button>
                        <button class="outline bg-card-edit-button" id="faculty-class-schedule-edit-button">Edit</button>
                        <button class="outline bg-card-view-button" id="faculty-class-schedule-view-button">View</button>
                        <button class="outline bg-card-pdf-button">PDF</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <?php if (in_array($logged_role_id, $primary_roles) || in_array($logged_role_id, $higher_official)) { ?>
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="dept-filter">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto dept-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="dept_filter[]" class="dept-filter" value="0">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="year-of-study-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto year-of-study-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                            <label class="input-label">Select The Year Of Study</label>
                            <input type="hidden" name="year_of_study_filter" class="year-of-study-filter" value="0">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="section-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto section-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                            <label class="input-label">Select The Section</label>
                            <input type="hidden" name="section_filter" class="section-filter" value="0">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#faculty-class-schedule-add-button').on('click', async function(event) {
            await showComponentLoading(1)
            event.preventDefault();
            params = '?action=add&route=faculty';
            const newUrl = window.location.origin + window.location.pathname + params;
            history.pushState({
                action: 'add',
                route: 'faculty'
            }, '', newUrl);
            await load_main_components();
            setTimeout(() => {
                hideComponentLoading();
            }, 500);

        });

        $('#faculty-class-schedule-view-button').on('click', async function(event) {
            await showComponentLoading(1)
            event.preventDefault();
            params = '?action=view&route=faculty';
            const newUrl = window.location.origin + window.location.pathname + params;
            history.pushState({
                action: 'view',
                route: 'faculty'
            }, '', newUrl);
            await load_main_components();
            setTimeout(() => {
                hideComponentLoading();
            }, 500);

        });

        $('.dept-filter-dummy').on('click focus', function() {
            fetch_dept_list($(this));
        });

        $('.year-of-study-filter-dummy').on('click', function() {

            if ([...<?= json_encode($primary_roles) ?>, ...<?= json_encode($higher_official) ?>].includes(<?= $logged_role_id ?>)) {

                fetch_year_list($(this), $(".dept-filter").val());
            } else {

                fetch_year_list($(this), <?= $logged_dept_id ?>);
            }
        });

        $('.year-of-study-filter').on('change', function() {
            $('.section-filter-dummy').val("");
            $('.section-filter').val(0);
        });

        $('.section-filter-dummy').on('click', function() {
            fetch_section_list($(this), $(".year-of-study-filter").val());
        });

        $('.section-filter-dummy').on('blur', function() {

            setTimeout(() => {
                if ($(".section-filter").val()) {
                    if ([...<?= json_encode($primary_roles) ?>, ...<?= json_encode($higher_official) ?>].includes(<?= $logged_role_id ?>)) {
                        load_dept_view_timetable($(".dept-filter").val(), $(".year-of-study-filter").val(), $(".section-filter").val());
                    } else {
                        load_dept_view_timetable(<?= $logged_dept_id ?>, $(".year-of-study-filter").val(), $(".section-filter").val());
                    }
                }
            }, 200);
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>