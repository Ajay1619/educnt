<?php
include_once('../../../../config/sparrow.php');

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
                        <h2 id="action"></h2>
                    </div>
                    <?php if (in_array($logged_role_id, $main_roles)) { ?>
                    <?php } else { ?>
                        <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                            <button id="add_admission" class="outline bg-card-button">New Admission</button>
                            <button class="outline" id="student-admission-excel">Excel</button>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <!-- <div class="col-3 col-sm-4 col-xs-6">
                        <select name="faculty_student_status" id="faculty-student-status">
                            <option value="1">Status</option>
                            <option value="2">Accepted</option>
                            <option value="3"></option>
                            <option value="4">Year 4</option>
                        </select>
                    </div> -->
                    <div class="col col-4  col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto faculty-status-filter-dummy dropdown-input" placeholder=" ">
                            <label class="input-label">Select The Status</label>
                            <input type="hidden" name="faculty_status_filter[]" class="faculty-status-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                    <div class="col col-4  col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto faculty-admissionyear-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Admission Year</label>
                            <input type="hidden" name="faculty_admissionyear_filter[]" class="faculty-admissionyear-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <!-- <div class="col-3 col-sm-4 col-xs-6">
                        <select name="faculty_student_academic_year" id="faculty-student-academic-year">

                            <option value="1">Academic</option>
                            <option value="2">2020-2021</option>
                            <option value="3">2021-2022</option>
                            <option value="4">2022-2023</option>
                            <option value="4">2023-2024</option>
                        </select>
                    </div>  -->
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#add_admission').on('click', function() {
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'personal',
                    tab: 'personal'
                };

                // Construct the new URL with query parameters
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                // Use pushState to set the new URL and pass params as the state object
                window.history.pushState(params, '', newUrl);
                //load_personal_info_components();
                loadComponentsBasedOnURL()
            });


            $('.faculty-status-filter-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_status(element)
            });

            $('#student-admission-excel').on('click', function() {
                var academicbatch = 0;
                if ($('.faculty-academicbatch-filter').val()) {
                    academicbatch = $('.faculty-academicbatch-filter').val();
                }
                var admissionyear = 0;
                if ($('.faculty-admissionyear-filter').val()) {
                    admissionyear = $('.faculty-admissionyear-filter').val();
                }
                print_student_admission_pdf($('.faculty-status-filter').val(), admissionyear, academicbatch)
            });

            // Change event for hidden input
            $('.faculty-status-filter-dummy').on('blur', function() {

                //settimeout function
                setTimeout(() => {
                    var academicbatch = 0;
                    if ($('.faculty-academicbatch-filter').val()) {
                        academicbatch = $('.faculty-academicbatch-filter').val();
                    }
                    var admissionyear = 0;
                    if ($('.faculty-admissionyear-filter').val()) {
                        admissionyear = $('.faculty-admissionyear-filter').val();
                    }

                    //faculty_overall_profile_table($('.faculty-designation-filter').val(), dept);
                    load_faculty_overall_admission_table($('.faculty-status-filter').val(), admissionyear, academicbatch)
                }, 200);

            });



            $('.faculty-admissionyear-filter-dummy').on('click', function() {
                // fetch_dept_list($(this));
                showToast('warning', "Admission year is unavailable for filtering.");

            });


            $('.faculty-admissionyear-filter-dummy').on('blur', function() {

                // setTimeout(() => {
                //     var desg = 0;
                //     if ($('.faculty-designation-filter').val()) {
                //         desg = $('.faculty-designation-filter').val();
                //     }

                //     //faculty_overall_profile_table(desg, $('.faculty-dept-filter').val());
                //     load_faculty_overall_profile_table(desg, $('.faculty-dept-filter').val())
                // }, 100);

            });


            // Call the function to update the heading
            callAction();

        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>