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

    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Mentor Reset Form
            </div>

            <!-- Popup Content -->
            <div class="popup-content  mentor-allotment-form">
                <div class="row align-items-center">
                    <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto faculty-mentor-name-dummy autocomplete-input" placeholder=" " value="">
                            <label class="input-label">Select The Faculty Name</label>
                            <input type="hidden" name="faculty_id[]" class="faculty-id" value="0">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col">
                        <div class="chip-container" id="faculty-mentor-list-chips"></div>
                    </div>
                </div>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <div class="popup-params.action-buttons">
                    <button class="btn-success mentor-reset-go-btn">Yes, Confirm</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        //document.ready function
        $(document).ready(async function() {
            var faculty_mentor_name_list = [];
            $('.faculty-mentor-name-dummy').on('click', function() {
                const element = $(this);
                fetch_faculty_name_list(element, 0);
            });


            $('.faculty-mentor-name-dummy').on('input', function() {
                const element = $(this);
                const suggestions = element.siblings(".autocomplete-suggestions");
                const value = element.siblings(".faculty-id");


                // Get the input text
                const inputText = element.val().toLowerCase();
                // Filter faculty_name_list based on the input
                const filteredFacultyList = faculty_name_list.filter(faculty =>
                    faculty.title.toLowerCase().includes(inputText) ||
                    faculty.code.toLowerCase().includes(inputText)
                );
                // Pass the filtered list to showSuggestions
                showSuggestions(filteredFacultyList, suggestions, value, element);
            });


            $('.faculty-mentor-name-dummy').on('blur', function() {
                setTimeout(() => {
                    if ($('.faculty-id').val() != 0) {
                        createChip($(this), $('#faculty-mentor-list-chips'), $('.faculty-id').val());

                        $(this).val(""); // Clear the input field
                        $('.faculty-id').val("0"); // Clear the input field
                        faculty_mentor_name_list = getChipsValues($('#faculty-mentor-list-chips'));
                    }

                }, 100);
            });

            $('.mentor-reset-go-btn').on('click', function(e) {
                const faculty_id = getChipsId($('#faculty-mentor-list-chips'))
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/faculty_mentor_role_reset_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: {
                            'faculty_id': faculty_id,
                            'dept_id': <?= $logged_dept_id ?>
                        },
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        },
                        success: async function(response) {
                            response = JSON.parse(response)
                            showToast(response.status, response.message);
                            const urlParams = new URLSearchParams(window.location.search);
                            const route = urlParams.get('route');
                            const type = urlParams.get('type');

                            const params = {
                                action: 'view',
                                route: route,
                                type: type,
                            };

                            // Construct the new URL with query parameters
                            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                            const newUrl = window.location.origin + window.location.pathname + queryString;
                            // Use pushState to set the new URL and pass params as the state object
                            window.history.pushState(params, '', newUrl);
                            loadComponentsBasedOnURL()
                            resolve(); // Resolve the promise
                        },
                        error: function(jqXHR) {
                            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                            showToast('error', message);
                            reject(); // Reject the promise

                        }
                    });
                });
            });
        });
        <?php
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }
        ?>