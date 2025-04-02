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
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

?>
    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <?php if (!in_array($logged_role_id, $main_roles)) { ?>

                            <button class="outline bg-card-add-button" id="add_btn">&#10012 Add</button>
                        <?php } ?>
                        <button class="outline bg-card-view-button" id="view_btn">view</button>
                        <button class="outline">EXCEL</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <div class="col-3 col-sm-4 col-xs-6 achievement_type">
                        <div class="input-container">
                            <div class="input-container dropdown-container ">
                                <input type="text" id="achievement-bg-card-dummy" name="achievements" class="auto dropdown-input" placeholder=" " readonly required>
                                <label class="input-label" for="achievement-bg-card-dummy">Select achievement Type</label>
                                <input type="hidden" name="achievement_type" id="achievement">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="achievement-suggestions"></div>
                            </div>
                        </div>


                    </div>
                    <?php if (in_array($logged_role_id, $primary_roles)) { ?>
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12 department" id="dept">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto faculty-dept-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="faculty_dept_filter[]" id="department" class="faculty-dept-filter faculty-dept">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    <?php } ?>

                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty' 
            let id = urlParams.get('id');
         if (action == 'view' && route == 'faculty' && type == 'overall' && !id) {
                $('.achievement_type').hide();
                $('.department').show();
            }else if(action == 'view' && route == 'faculty' && type == 'overall' && id)
            {
                $('.achievement_type').show();
                $('.department').hide();
            }


            $('#achievement-bg-card-dummy').on('click focus', async function() {

                await fetch_achivements($(this));
            });
            $('.faculty-dept-filter-dummy').on('click focus', function() {
                fetch_dept_list($(this));
            });
            $('.faculty-dept-filter-dummy').on('blur', function() {
                //settimeout function
                setTimeout(() => {
                    load_faculty_overall_achievements_table(0, $('#department').val());
                }, 150);

            });
            $('#achievement-bg-card-dummy').on('blur', function() {
                //settimeout function
                setTimeout(() => {
                    fac_id = "";
                    if (id != null) {
                        fac_id = id;
                    } else {
                        fac_id = '<?= encrypt_data($logged_user_id) ?>';

                    }
                    fetchFacultyAchivementData($('#achievement').val(), fac_id);
                }, 150);

            });

        });

        // Call the function to update the heading and buttons
        callAction($("#action"));
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>