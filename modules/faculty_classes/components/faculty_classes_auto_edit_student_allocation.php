<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>

    <p class="action-text info-text">
        ⓘ Please Select the Current Academic batch and Section Of the Students To Promote To Your Class.
    </p>
    <form id="faculty-classes-auto-edit-student-allocation-table-form" class="m-6 p-6" method="POST">
        <div class="row justify-center m-6">
            <!-- Student's batch -->
            <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" class="auto student-batch-dummy dropdown-input" placeholder=" " value="">
                    <label class="input-label">Select The Student's Batch</label>
                    <input type="hidden" name="previous_student_batch_id" class="student-batch-id" value="">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="dropdown-suggestions"></div>
                </div>
            </div>
            <!-- Year Of Study -->
            <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <input type="hidden" id="year-of-study-id" name="previous_year_of_study_id" value="0">
                <div class="input-container">
                    <input type="text" id="year-of-study-title" name="previous_year_of_study_title" placeholder="" readonly>
                    <label class="input-label">Current Year of Study</label>
                </div>
            </div>
            <!-- Section -->
            <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" class="auto section-dummy dropdown-input" placeholder=" " value="" readonly required>
                    <label class="input-label">Select Section</label>
                    <input type="hidden" name="previous_section_id" class="section-id" value="">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="dropdown-suggestions"></div>
                </div>
            </div>
            <button type="submit" class="primary text-center full-width m-6">SAVE</button>
        </div>
    </form>
    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.student-batch-dummy').on('click', function() {
                    fetch_student_batch_list($(this))
                })

                $('.section-dummy').on('click', function() {
                    assign_section_to_dropdown($(this))
                })

                $('.student-batch-dummy').on('blur', async function() {
                    try {
                        showComponentLoading();
                        setTimeout(() => {
                            if ($('.student-batch-id').val() != '') {
                                fetch_year_of_study_and_section_with_academic_batch_class_allotment($('.student-batch-id').val())
                            }
                        }, 100)
                    } catch (error) {
                        // get error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    } finally {
                        // Hide the loading screen once all operations are complete
                        setTimeout(function() {
                            hideComponentLoading(); // Delay hiding loading by 1 second
                        }, 100)
                    }
                })

                $('#faculty-classes-auto-edit-student-allocation-table-form').submit(async function(e) {
                    e.preventDefault(); // Prevent the default form submission behavior

                    try {
                        showComponentLoading();

                        // Serialize the form data
                        const formData = $(this).serialize();

                        // Perform AJAX request
                        $.ajax({
                            type: 'POST',
                            url: '<?= MODULES . '/faculty_classes/ajax/faculty_classess_auto_student_allocation_table_form.php' ?>',
                            headers: {
                                'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            },
                            data: {
                                'location': window.location.href,
                                'form_data': formData,
                                'sem_duration_id': sem_duration_id,
                                'sem_id': sem_id,
                                'academic_year_id': academic_year_id,
                                'year_of_study_id': year_of_study_id,
                                'section_id': section_id

                            },
                            success: function(response) {
                                response = JSON.parse(response);
                                showToast(response.status, response.message);
                                if (response.code == 200) {
                                    // Reload the page after success
                                    setTimeout(() => {
                                        location.reload();
                                    }, 500);
                                }
                            },
                            error: function(jqXHR) {
                                const message =
                                    jqXHR.status == 401 ?
                                    'Unauthorized access. Please check your credentials.' :
                                    'An error occurred. Please try again.';
                                showToast('error', message);
                            },
                            complete: function() {
                                // Hide the loading screen regardless of success or error
                                hideComponentLoading();
                            },
                        });
                    } catch (error) {
                        const errorMessage = error.message || 'An error occurred while submitting the form.';
                        await insert_error_log(errorMessage);
                        await load_error_popup();
                        console.error('An error occurred while submitting:', error);
                    } finally {
                        setTimeout(function() {
                            hideComponentLoading();
                        }, 100);
                    }
                });

            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100)
            }
        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>