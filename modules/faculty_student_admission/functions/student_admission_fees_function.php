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

?>
    <script>
        const loadfeesComponentsBasedOnURL = async (student_id) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'

            if (action == 'add' && route == 'faculty' && type == 'fees') {
                if (tab == 'fees_details') {

                    load_fees(student_id);
                    $('.tab-btn.fees').addClass('active');
                    $('.tab-btn.concession').removeClass('active');
                } else if (tab == 'concession_details') {

                    load_concession_fees();
                    $('.tab-btn.fees').removeClass('active');
                    $('.tab-btn.concession').addClass('active');
                    // $('.tab-btn.personal').addClass('active').css('background-color', 'var(--success-dark)');
                }
            } else {
                console.error('No matching condition for route and action');
            }
        };



        const load_fees = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/fees_info/faculty_admission_fees_details.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const load_concession_fees = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/fees_info/student_admission_fees_concession.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {

                        $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const fetch_student_fees_structure = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/json/fetch_student_fees_structure.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'type': 1
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            populate_fees_data(data)
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const fetch_student_concession_structure = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/json/fetch_student_concession_structure.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'type': 1
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            populate_concession_data(data)
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const populate_concession_data = (data) => {
            const years = ['Ist', 'IInd', 'IIIrd', 'IVth'];

            years.forEach(year => {
                const tableBody = $(`#${year}-year-concession .concession-table-body`);
                tableBody.empty(); // Clear default row

                // Filter data for the current year
                const yearData = data.filter(item => item.concession_year === year);

                if (yearData.length > 0) {
                    yearData.forEach(concession => {
                        const newRow = `
                    <tr class="concession-row">
                        <td>
                            <div class="input-container">
                                <input type="text" name="${year}_concession_category[]" class="${year}-concession-category" placeholder=" " value="${concession.concession_category}" required>
                                <label class="input-label">Enter Concession Category</label>
                            </div>
                        </td>
                        <td>
                            <div class="input-container">
                                <input type="text" name="${year}_concession_amount[]" class="${year}-concession-amount" placeholder=" " value="${concession.concession_amount}" required>
                                <label class="input-label">Enter Concession Amount</label>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="icon tertiary remove-concession-row" data-year="${year}">X</button>
                        </td>
                    </tr>
                `;
                        tableBody.append(newRow);
                    });
                } else {
                    // Add default empty row if no concession data exists
                    const defaultRow = `
                <tr class="concession-row">
                    <td>
                        <div class="input-container">
                            <input type="text" name="${year}_concession_category[]" class="${year}-concession-category" placeholder=" " required>
                            <label class="input-label">Enter Concession Category</label>
                        </div>
                    </td>
                    <td>
                        <div class="input-container">
                            <input type="text" name="${year}_concession_amount[]" class="${year}-concession-amount" placeholder=" " required>
                            <label class="input-label">Enter Concession Amount</label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="icon tertiary remove-concession-row" data-year="${year}">X</button>
                    </td>
                </tr>
            `;
                    tableBody.append(defaultRow);
                }
            });

            // Attach event listeners for removing rows
            $('.remove-concession-row').off('click').on('click', function() {
                const year = $(this).data('year');
                const rowCount = $(`#${year}-year-concession .concession-row`).length;
                if (rowCount > 1) {
                    $(this).closest('tr.concession-row').remove();
                }
            });
        };


        const populate_fees_data = (data) => {
            // Populate the fee tables with fetched data
            const years = ['Ist', 'IInd', 'IIIrd', 'IVth'];
            years.forEach(year => {
                const tableBody = $(`#${year}-year-fees .fee-table-body`);
                tableBody.empty(); // Clear the default row

                // Filter data for the current year
                const yearData = data.filter(item => item.fees_year === year);

                if (yearData.length > 0) {
                    yearData.forEach(fee => {
                        const newRow = `
                                    <tr class="fee-row">
                                        <td>
                                            <div class="input-container autocomplete-container">
                                                <input type="text" class="autocomplete-input ${year}-fee-category-input" placeholder=" " value="${fee.fees_category_title}" required>
                                                <label class="input-label" for="${year}-fee-category-input">Select Fee Category</label>
                                                <input type="hidden" name="${year}_fee_category[]" class="${year}-fee-category-value fee-category-value" value="${fee.fees_category}" required>
                                                <span class="autocomplete-arrow">⌄</span>
                                                <div class="autocomplete-suggestions ${year}-fee-categories-suggestions"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-container">
                                                <input type="text" name="${year}_fee_amount[]" class="${year}-fee-amount" placeholder=" " value="${fee.fees_amount}" required>
                                                <label class="input-label">Enter Fee Amount</label>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="icon tertiary remove-fee-row" data-year="${year}">X</button>
                                        </td>
                                    </tr>
                                `;
                        tableBody.append(newRow);
                    });
                } else {
                    // Add back the default empty row if no data exists for this year
                    const defaultRow = `
                                <tr class="fee-row">
                                    <td>
                                        <div class="input-container autocomplete-container">
                                            <input type="text" class="autocomplete-input ${year}-fee-category-input" placeholder=" " required>
                                            <label class="input-label" for="${year}-fee-category-input">Select Fee Category</label>
                                            <input type="hidden" name="${year}_fee_category[]" class="${year}-fee-category-value fee-category-value" required>
                                            <span class="autocomplete-arrow">⌄</span>
                                            <div class="autocomplete-suggestions ${year}-fee-categories-suggestions"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-container">
                                            <input type="text" name="${year}_fee_amount[]" class="${year}-fee-amount" placeholder=" " required>
                                            <label class="input-label">Enter Fee Amount</label>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="icon tertiary remove-fee-row" data-year="${year}">X</button>
                                    </td>
                                </tr>
                            `;
                    tableBody.append(defaultRow);
                }
            });

            // Reattach event listeners for newly added rows
            $('.remove-fee-row').off('click').on('click', function() {
                const year = $(this).data('year');
                const rowCount = $(`#${year}-year-fees .fee-row`).length;
                if (rowCount > 1) {
                    $(this).closest('tr.fee-row').remove();
                }
            });

            $('.autocomplete-input').off('click input').on('click input', async function(e) {
                fetch_fees_category($(this), 1);
            });
        }
        const fetch_fees_category = (element, type) => {
            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_fees_category.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'type': type
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const fees_category = response.data;
                            const suggestions = element.siblings(".autocomplete-suggestions");
                            const value = element.siblings(".fee-category-value");

                            // Get the input text
                            const inputText = element.val().toLowerCase();
                            // Filter student_name_list based on the input
                            const fees_category_list = fees_category.filter(fees =>
                                fees.title.toLowerCase().includes(inputText)
                            );
                            // Pass the filtered list to showSuggestions
                            showSuggestions(fees_category_list, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
