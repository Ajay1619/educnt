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
    <div class="row align-center">

        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="input-container dropdown-container">
                <input type="text" class="auto student-batch-dummy dropdown-input" placeholder=" " value="">
                <label class="input-label">Select The Student's Batch</label>
                <input type="hidden" name="student_batch_id" class="student-batch-id" value="">
                <span class="dropdown-arrow">&#8964;</span>
                <div class="dropdown-suggestions"></div>
            </div>
        </div>
    </div>
    <div class="row align-center" id="student-statistics"></div>

    <form id="faculty-classes-manual-edit-student-allocation-table-form" method="POST">
        <div class="curvy-table-container mt-6 flex-column">
            <div class="class-student-assignment m-6 flex-container justify-center">
                <p class="action-text">
                    Select a batch to view the list of students for your class.
                </p>
                <div class="action-hint">
                    *"As Professor Snape would say, 'Leadership requires strength, whether facing enemies or allies.' Your guidance shapes the future of these students."*
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                //id=student-batch-dummy onclick
                $('.student-batch-dummy').on('click', function() {
                    fetch_student_batch_list($(this))
                })

                $('.student-batch-dummy').on('blur', async function() {
                    try {
                        showComponentLoading();
                        setTimeout(() => {
                            if ($('.student-batch-id').val() != '') {
                                load_student_list_table($('.student-batch-id').val())
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

                $('#faculty-classes-manual-edit-student-allocation-table-form').submit(async function(e) {
                    e.preventDefault();
                    try {
                        showComponentLoading();
                        return new Promise((resolve, reject) => {
                            const formData = $(this).serialize()
                            $.ajax({
                                type: 'POST',
                                url: '<?= MODULES . '/faculty_classes/ajax/faculty_classess_manual_student_allocation_table_form.php' ?>',
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
                                    'section_id': section_id,
                                    'academic_batch_id': $(".student-batch-id").val()
                                },
                                success: function(response) {
                                    response = JSON.parse(response)
                                    showToast(response.status, response.message);
                                    if (response.code == 200) {
                                        setTimeout(() => {
                                            location.reload()
                                        }, 500);
                                    }
                                    resolve();
                                },
                                error: function(jqXHR) {
                                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                                    showToast('error', message);
                                }
                            });
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