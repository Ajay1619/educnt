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
    <form id="faculty-classes-class-advisor-subject-allocation-form" method="post">
        <div id="subject-list">
            <div class="row align-center">
                <input type="hidden" name="previous_subject_allocation_id[]" value="0">
                <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container autocomplete-container">
                        <input type="text" class="auto subject-name-dummy autocomplete-input" placeholder=" " value="">
                        <label class="input-label">Select The Subject Name</label>
                        <input type="hidden" name="subject_id[]" class="subject-id" value="">
                        <span class="autocomplete-arrow">&#8964;</span>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                </div>
                <div class="col col-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" class="auto room-name-dummy dropdown-input" placeholder=" " value="" readonly>
                        <label class="input-label">Select The Room Name</label>
                        <input type="hidden" name="room_id[]" class="room-id" value="">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions"></div>
                    </div>
                </div>
                <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container autocomplete-container">
                        <input type="text" class="auto faculty-name-dummy autocomplete-input" placeholder=" " value="">
                        <label class="input-label">Select The Faculty Name</label>
                        <input type="hidden" name="faculty_id[]" class="faculty-id" value="">
                        <span class="autocomplete-arrow">&#8964;</span>
                        <div class="autocomplete-suggestions"></div>
                    </div>
                </div>
                <div class="col col-1 col-lg-1 col-md-6 col-sm-6 col-xs-12">
                    <button type="button" class="icon tertiary remove-subject-btn" data-previous-allocated-subject-id="0">X</button>
                </div>
            </div>
        </div>

        <div class="add-another-subject flex-container align-center underline">
            <span class="add-subject cursor-pointer ">Add Another Subject
                <button type="button" class="icon tertiary add-subject-btn">+</button>
            </span>
        </div>
        <button type="submit" class="primary text-center full-width">SAVE</button>
    </form>
    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                await fetch_subject_allocation_data();

                $('.subject-name-dummy').on('click input', async function(e) {
                    fetch_subject_name_list($(this), <?= $logged_dept_id ?>, year_of_study_id, sem_id)
                })

                $('.room-name-dummy').on('click ', async function(e) {
                    fetch_room_name_list($(this), <?= $logged_dept_id ?>)
                })

                $('.faculty-name-dummy').on('click ', async function(e) {
                    fetch_faculty_name_list($(this), <?= $logged_dept_id ?>)
                })

                $('.add-subject').on('click ', async function(e) {
                    add_another_subject()
                })

                $('#faculty-classes-class-advisor-subject-allocation-form').submit(async function(e) {
                    e.preventDefault();
                    try {
                        showComponentLoading();
                        return new Promise((resolve, reject) => {
                            const formData = $(this).serialize()
                            $.ajax({
                                type: 'POST',
                                url: '<?= MODULES . '/faculty_classes/ajax/faculty_classes_class_advisor_subject_allocation_form.php' ?>',
                                headers: {
                                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                                },
                                data: {
                                    'location': window.location.href,
                                    'form_data': formData,
                                    'sem_duration_id': sem_duration_id,
                                    'year_of_study_id': year_of_study_id,
                                    'section_id': section_id,
                                    'academic_year_id': academic_year_id,
                                    'sem_id': sem_id
                                },
                                success: function(response) {
                                    response = JSON.parse(response)
                                    showToast(response.status, response.message);
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