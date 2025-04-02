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
        <div class="alert-popup mentor-swap-popup">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Mentor Swap Form
            </div>

            <!-- Popup Content -->
            <div class="popup-content  mentor-allotment-form">
                <div class="row align-items-center">
                    <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto faculty-mentor-name-dummy autocomplete-input" placeholder=" " value="">
                            <label class="input-label">Select The Faculty Name</label>
                            <input type="hidden" name="from_faculty_id[]" id="from-faculty-id" class="faculty-id" value="0">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <img id="swap-svg" src="<?= GLOBAL_PATH . '/images/svgs/arrow-down.svg' ?>" alt="">
                </div>
                <div class="row align-items-center">
                    <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto faculty-mentor-name-dummy autocomplete-input" placeholder=" " value="">
                            <label class="input-label">Select The Faculty Name</label>
                            <input type="hidden" name="to_faculty_id[]" id="to-faculty-id" class="faculty-id" value="0">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <div class="popup-params.action-buttons">
                    <button class="btn-success mentor-swap-go-btn">Yes, Swap</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        //document.ready function
        $(document).ready(async function() {
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


            $('.mentor-swap-go-btn').on('click', function(e) {
                var location_href = window.location.href;
                return new Promise((resolve, reject) => {
                    const formData = $(this).serialize();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_roles_responsibilities/ajax/faculty_mentor_role_swap_form.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: {
                            'from_faculty_id': $("#from-faculty-id").val(),
                            'to_faculty_id': $("#to-faculty-id").val(),
                            'location_href': location_href
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