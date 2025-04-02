<?php
include_once('../../../../../config/sparrow.php');

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
    <div class="tab-content active" data-tab="0">
        <form id="student-fees-structure-admission" method="POST">
            <?php
            $years = ['Ist', 'IInd', 'IIIrd', 'IVth'];
            foreach ($years as $year) {
            ?>
                <div class='section-header-title text-left m-6'><?php echo $year; ?> Year Fees</div>
                <table class="fee-table" id="<?php echo $year; ?>-year-fees">
                    <thead>
                        <tr>
                            <th>Fees Categories</th>
                            <th>Fees Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="fee-table-body">
                        <tr class="fee-row">
                            <td>
                                <div class="input-container autocomplete-container">
                                    <input type="text" class="autocomplete-input <?php echo $year; ?>-fee-category-input" placeholder=" " required>
                                    <label class="input-label" for="<?php echo $year; ?>-fee-category-input">Select Fee Category</label>
                                    <input type="hidden" name="<?php echo $year; ?>_fee_category[]" class="<?php echo $year; ?>-fee-category-value fee-category-value" required>
                                    <span class="autocomplete-arrow">⌄</span>
                                    <div class="autocomplete-suggestions <?php echo $year; ?>-fee-categories-suggestions"></div>
                                </div>
                            </td>
                            <td>
                                <div class="input-container">
                                    <input type="text" name="<?php echo $year; ?>_fee_amount[]" class="<?php echo $year; ?>-fee-amount" placeholder=" " required>
                                    <label class="input-label">Enter Fee Amount</label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="icon tertiary remove-fee-row" data-year="<?php echo $year; ?>">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class=" flex-container align-center underline ">
                    <span class="add-fee-btn add-another-fees  cursor-pointer" data-year="<?php echo $year; ?>">Add Another Category
                        <button type="button" class="icon tertiary add-fee-row">+</button>
                    </span>
                </div>
            <?php } ?>
        </form>
    </div>
    <div class="form-navigation">
        <button class="btn prev-btn text-left" id="fees_info_faculty_form_prev_btn" type="button">Previous</button>
        <button class="btn next-btn text-right" id="fees_info_faculty_form_nxt_btn" type="submit">Next</button>
    </div>

    <script>
        $(document).ready(async function() {

            await fetch_student_fees_structure();
            $('.autocomplete-input').on('click input', async function(e) {
                fetch_fees_category($(this), 1)
            })

            // Add new fee row
            $('.add-another-fees').on('click', function() {
                const year = $(this).data('year');
                const newRow = `
                    <tr class="fee-row">
                        <td>
                            <div class="input-container autocomplete-container">
                                <input type="text" class="autocomplete-input ${year}-fee-category-input" placeholder=" " required>
                                <label class="input-label">Select Fee Category</label>
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
                $(`#${year}-year-fees .fee-table-body`).append(newRow);
                $('.remove-fee-row').on('click', function() {
                    const year = $(this).data('year');
                    const rowCount = $(`#${year}-year-fees .fee-row`).length;
                    if (rowCount > 1) {
                        $(this).closest('tr.fee-row').remove();
                    }
                });

                $('.autocomplete-input').on('click input', async function(e) {
                    fetch_fees_category($(this), 1)
                })
            });

            // Remove fee row


            // Previous button click handler
            $('#fees_info_faculty_form_prev_btn').on('click', async function() {
                showComponentLoading(1);
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'course',
                    tab: 'course'
                };
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                window.history.pushState(params, '', newUrl);
                await loadUrlBasedOnURL();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100);
            });



            $('#fees_info_faculty_form_nxt_btn').on('click', async function(e) {
                showComponentLoading(2)
                e.preventDefault();
                const formData = new FormData($('#student-fees-structure-admission')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/student_fees_structure_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },

                    success: async function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            console.log(response)
                            showToast('success', response.message);

                            const params = {
                                action: 'add',
                                route: 'faculty',
                                type: 'fees',
                                tab: 'concession_details'
                            };
                            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                            const newUrl = window.location.origin + window.location.pathname + queryString;
                            window.history.pushState(params, '', newUrl);
                            await loadUrlBasedOnURL();
                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>