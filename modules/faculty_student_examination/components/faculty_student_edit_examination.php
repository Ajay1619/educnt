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
   $examdetails = $_GET;
?>
<div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup half-width">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Select Slot
            </div>

            <!-- Popup Content -->
            <div class="popup-content">
            <form id="form_exam_management" method="POST">
                <input type="hidden" name="exam_id" id="exam_id" class="exam_id">
                <div class="row">
                    <!-- Exam Title -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="selected-exam-group-dummy" name="selected_exam_group" class="selected_exam_group auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="selected-exam-group-dummy">Exam Group</label>
                            <input type="hidden" name="exam_group_id" class="selected-exam-group-filter" id="exam_group_id" required>
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>


                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" id="selected-exam-type-dummy" name="selected_exam_type" class="selected_exam_type auto dropdown-input" placeholder=" " readonly>
                            <label class="input-label" for="selected-exam-type-dummy">Exam Type</label>
                            <input type="hidden" name="exam_type_id" class="selected-exam-type-filter" id="exam_type_id" required>
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                        <div class="input-container date">
                            <input type="date" value="" class="bulmaCalender" id="examStartDate" name="examStartDate" placeholder="dd-MM-yyyy" required aria-required="true">
                            <label class="input-label " for="examStartDate">Exam Start Date</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                        <div class="input-container date">
                            <input type="date" value="" class="bulmaCalender" id="examendDate" name="examendDate" placeholder="dd-MM-yyyy" required aria-required="true">
                            <label class="input-label " for="examendDate">Exam End Date</label>
                        </div>
                    </div>
                    <!-- Exam Max Marks -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="number" id="exam_max_marks" name="exam_max_marks" class="input-field" placeholder=" " step="0.01" required>
                            <label class="input-label" for="exam_max_marks">Max Marks</label>
                        </div>
                    </div>

                    <!-- Exam Min Marks -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="number" id="exam_min_marks" name="exam_min_marks" class="input-field" placeholder=" " step="0.01" required>
                            <label class="input-label" for="exam_min_marks">Min Marks</label>
                        </div>
                    </div>

                    <!-- Exam Duration -->
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container">
                            <input type="number" id="exam_duration" name="exam_duration" class="input-field" placeholder=" " step="0.01" required>
                            <label class="input-label" for="exam_duration">Exam Duration (in hours)</label>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto selected-department-dummy autocomplete-input" placeholder=" " value="">
                            <label class="input-label">Select The Department</label>
                            <input type="hidden" name="selected_department" class="selected-department">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="chip-container" id="selected-department-list-chips"></div>
                            </div>
                        </div>
                    </div>




                </div>


                <!-- Submit Button -->
                <div class="row mt-6">
                    <div class="col col-12">
                        <button class="nav-next text-center full-width">Submit</button>
                    </div>
                </div>
            </form>
            </div> 
        </div>
    </div>

    <script>
        $(document).ready(async function()  { 
          await  fetch_exam_data(<?php echo json_encode($examdetails); ?>);
            
            $(".selected_exam_group").on("click", function() {
                selected_exam_group($(this));
            });

            $(".selected_exam_type").on("click", function() {
                load_exam_list($(this), $('#exam_group_id').val());
            });


            $('.individual-year-list').on('click', async function() {
                // Remove 'active' class from all other .individual-year-list elements
                $('.individual-year-list').removeClass('active');

                // Add 'active' class to the clicked element
                $(this).addClass('active');
            });

            $('#form_exam_management').on('submit', async function(e) {
                e.preventDefault();
                const selected_dept_list = getChipsId($('#selected-department-list-chips'));
                const exam_group_id = $("#exam_group_id").val();
                const exam_type_id = $("#exam_type_id").val();
                const examStartDate = $("#examStartDate").val();
                const examendDate = $("#examendDate").val();
                const exam_duration = $("#exam_duration").val();
                const exam_max_marks = $("#exam_max_marks").val();
                const exam_min_marks = $("#exam_min_marks").val();
                const exam_id = $("#exam_id").val();
                const formData = new FormData(this);
                const paramsa = {
                    formData,
                    selected_dept_list
                };
                const params = {
                    //formData,  FormData object
                    selected_dept_list, // Department list
                    exam_group_id, // Exam Group ID
                    exam_type_id, // Exam Type ID
                    examStartDate, // Exam Start Date
                    examendDate, // Exam End Date
                    exam_duration, // Exam Duration
                    exam_max_marks, // Exam Max Marks
                    exam_min_marks, // Exam Min Marks
                    exam_id // Exam Min Marks
                };
                console.log(params);
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_student_examination/ajax/faculty_update_examination.php' ?>',
                    data: params,

                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search

                    }, 
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                        } else {
                            showToast(response.status, response.message);
                        }
                        $('#add-exam-popup').html('');
                         load_exam_management_table();

                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            });



            $('.selected-department-dummy').on('click', function() {

                const element = $(this);
                fetch_dept_list(element);
            });
            $('.selected-department-dummy').on('blur', function() {
                    setTimeout(() => {
                        console.log($('.selected-department').val())
                        if ($('.selected-department').val() != 0) {
                            createChip($(this), $('#selected-department-list-chips'), $('.selected-department').val());

                            $(this).val(""); // Clear the input field
                            $('.selected-department').val("0"); // Clear the input field
                            selected_slots_list_chips = getChipsValues($('#selected-department-list-chips'));
                        }

                    }, 150);
                });


        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>