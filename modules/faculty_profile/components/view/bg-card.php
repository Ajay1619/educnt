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

?>

    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-button" id="facultyProfilePrint">PDF📃</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <div class="col col-4  col-lg-4 col-md-6 col-sm-6 col-xs-12" id="desig">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto faculty-designation-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Designation</label>
                            <input type="hidden" name="faculty_designation_filter[]" class="faculty-designation-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <?php if (in_array($logged_role_id, $primary_roles)) { ?>
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="dept">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto faculty-dept-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="faculty_dept_filter[]" class="faculty-dept-filter">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="year">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto faculty-year-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Year</label>
                            <input type="hidden" name="faculty_year_filter[]" class="faculty-year-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="section">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto faculty-section-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Section</label>
                            <input type="hidden" name="faculty_section_filter[]" class="faculty-section-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            const route = <?= json_encode($routing) ?>;
            if (route == "Student") {
                // console.log(route);
                $('#dept').show(); // Show 'dept' div
                $('#year').show(); // show 'faculty' div
                $('#section').show(); // show 'faculty' div
                $('#desig').hide(); // Hide 'faculty' div
            } else if (route == "Faculty") {

                $('#dept').show(); // Show 'dept' div
                $('#year').hide(); // show 'faculty' div
                $('#section').hide(); // show 'faculty' div
                $('#desig').show();
            }


            // Call the function to update the heading
            callAction($("#action"));

            const fetch_dept_list = (element) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const dept_list = response.data;
                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".faculty-dept-filter")
                                showSuggestions(dept_list, suggestions, value, element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_year_list = (element, dept_id) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_year_of_study.php' ?>',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        data: {
                            'dept_id': dept_id
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const year_list = response.data;
                                console.log(year_list);

                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".faculty-year-filter")
                                showSuggestions(year_list, suggestions, value, element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            const fetch_section_list = (element, year_id) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_section_list.php' ?>',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        data: {
                            'year_id': year_id
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const section_list = response.data;
                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".faculty-section-filter")
                                showSuggestions(section_list, suggestions, value, element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }

            const fetch_faculty_designation = (element) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_faculty_designation.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const designation_list = response.data;
                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".faculty-designation-filter")
                                showSuggestions(designation_list, suggestions, value, element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }

            $('.faculty-status-filter-dummy').on('blur', function() {

                //settimeout function
                setTimeout(() => {
                    var academicbatch = 0;
                    if ($('.faculty-academicbatch-filter').val()) {
                        academicbatch = $('.faculty-academicbatch-filter').val();
                    }
                    var academicyear = 0;
                    if ($('.faculty-admissionyear-filter').val()) {
                        admissionyear = $('.faculty-admissionyear-filter').val();
                    }

                    //faculty_overall_profile_table($('.faculty-designation-filter').val(), dept);
                    load_faculty_overall_admission_table($('.faculty-status-filter').val(), academicyear, academicbatch)
                }, 200);

            });

            // Dropdown input click/focus event
            $('.faculty-designation-filter-dummy').on('click focus', function() {
                const element = $(this);
                fetch_faculty_designation(element)
            });

            // Change event for hidden input
            $('.faculty-designation-filter-dummy').on('blur', function() {
                //settimeout function
                setTimeout(() => {
                    var dept = 0;
                    if ($('.faculty-dept-filter').val()) {
                        dept = $('.faculty-dept-filter').val();
                    }

                    //faculty_overall_profile_table($('.faculty-designation-filter').val(), dept);
                    load_faculty_overall_profile_table($('.faculty-designation-filter').val(), dept)
                }, 100);

            });



            $('.faculty-dept-filter-dummy').on('click focus', function() {
                fetch_dept_list($(this));
            });
            const logged_role_id = <?= $logged_role_id ?>;
            $('.faculty-year-filter-dummy').on('click', function() {

                if (<?= json_encode($primary_roles) ?>.includes(logged_role_id)) {

                    fetch_year_list($(this), $(".faculty-dept-filter").val());
                } else {

                    fetch_year_list($(this), <?= $logged_dept_id ?>);
                }
            });
            $('.faculty-section-filter-dummy').on('click', function() {
                // console.log($(".faculty-dept-filter").val());
                fetch_section_list($(this), $(".faculty-year-filter").val());
            });

            $('.faculty-dept-filter-dummy').on('blur', function() {
                setTimeout(() => {

                    if (route == "Student") {
                        var section = 0;
                        var year = 0;
                        if ($('.faculty-year-filter').val()) {
                            year = $('.faculty-year-filter').val();
                        }
                        if ($('.faculty-section-filter').val()) {
                            section = $('.faculty-section-filter').val();
                        }
                        load_student_overall_profile_table(year, section, $(".faculty-dept-filter").val());

                    } else if (route == "Faculty") {
                        var desg = 0;
                        if ($('.faculty-designation-filter').val()) {
                            desg = $('.faculty-designation-filter').val();
                        }

                        //faculty_overall_profile_table(desg, $('.faculty-dept-filter').val());
                        load_faculty_overall_profile_table(desg, $('.faculty-dept-filter').val())
                    }
                }, 200);

            });






            $('#facultyProfilePrint').on('click', function() {
                setTimeout(() => {

                    if (route == "Student") {
                        var section = 0;
                        var year = 0;
                        if ($('.faculty-year-filter').val()) {
                            year = $('.faculty-year-filter').val();
                        }
                        if ($('.faculty-section-filter').val()) {
                            section = $('.faculty-section-filter').val();
                        }
                        Print_student_pdf(year, section, $(".faculty-dept-filter").val());
                        console.log("student");

                    } else if (route == "Faculty") {
                        console.log("faculty");
                        var desg = 0;
                        if ($('.faculty-designation-filter').val()) {
                            desg = $('.faculty-designation-filter').val();
                        }

                        //faculty_overall_profile_table(desg, $('.faculty-dept-filter').val());
                        Print_faculty_pdf(desg, $('.faculty-dept-filter').val())
                    }
                }, 200);

            });

            $('.faculty-year-filter-dummy').on('blur', function() {
                setTimeout(() => {
                    if (<?= json_encode($primary_roles) ?>.includes(logged_role_id)) {
                        var section = 0;
                        var year = 0;
                        var dept = 0;
                        if ($('.faculty-year-filter').val()) {
                            year = $('.faculty-year-filter').val();
                        }
                        if ($('.faculty-section-filter').val()) {
                            section = $('.faculty-section-filter').val();
                        }
                        if ($('.faculty-dept-filter').val()) {
                            dept = $('.faculty-dept-filter').val();
                        }
                        load_student_overall_profile_table(year, section, dept);
                    } else {

                        var section = 0;
                        var year = 0;

                        if ($('.faculty-year-filter').val()) {
                            year = $('.faculty-year-filter').val();
                        }
                        if ($('.faculty-section-filter').val()) {
                            section = $('.faculty-section-filter').val();
                        }

                        load_student_overall_profile_table(year, section);
                    }
                }, 200);

            });
            $('.faculty-section-filter-dummy').on('blur', function() {
                setTimeout(() => {
                    var section = 0;
                    var year = 0;
                    var dept = 0;
                    if ($('.faculty-year-filter').val()) {
                        year = $('.faculty-year-filter').val();
                    }
                    if ($('.faculty-section-filter').val()) {
                        section = $('.faculty-section-filter').val();
                    }
                    if ($('.faculty-dept-filter').val()) {
                        dept = $('.faculty-dept-filter').val();
                    }
                    load_student_overall_profile_table(year, section, dept);

                }, 200);

            });
        });
    </script>



<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>