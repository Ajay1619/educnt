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
        <form id="student-concession-structure" method="POST">
            <?php
            $years = ['Ist', 'IInd', 'IIIrd', 'IVth'];
            foreach ($years as $index => $year) {
            ?>
                <div class='section-header-title text-left m-6'><?php echo $year; ?> Year Concession</div>
                <table class="fee-table" id="<?php echo $year; ?>-year-concession">
                    <thead>
                        <tr>
                            <th>Concession Categories</th>
                            <th>Concession Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="concession-table-body">
                        <tr class="concession-row">
                            <td>
                                <div class="input-container">
                                    <input type="text" name="<?php echo $year; ?>_concession_category[]" class="<?php echo $year; ?>-concession-category" placeholder=" " required>
                                    <label class="input-label">Enter Concession Category</label>
                                </div>
                            </td>
                            <td>
                                <div class="input-container">
                                    <input type="text" name="<?php echo $year; ?>_concession_amount[]" class="<?php echo $year; ?>-concession-amount" placeholder=" " required>
                                    <label class="input-label">Enter Concession Amount</label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="icon tertiary remove-concession-row" data-year="<?php echo $year; ?>">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="add-another-concession flex-container align-center cursor-pointer underline" data-year="<?php echo $year; ?>">
                    <span class="add-concession-btn" data-year="<?php echo $year; ?>">Add Another Concession
                        <button type="button" class="icon tertiary add-concession-row">+</button>
                    </span>
                </div>
            <?php } ?>
        </form>
    </div>
    <div class="form-navigation">
        <button class="btn prev-btn text-left" id="fees_concession_info_faculty_form_prev_btn" type="button">Previous</button>
        <button class="btn next-btn text-right" id="fees_concession_info_faculty_form_nxt_btn" type="submit">Next</button>
    </div>

    <script>
        $(document).ready(async function() {
            // Add new concession row
            await fetch_student_concession_structure();

            $('.add-another-concession').on('click', function() {
                const year = $(this).data('year');
                const newRow = `
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
                $(`#${year}-year-concession .concession-table-body`).append(newRow);
            });

            // Remove concession row
            $(document).on('click', '.remove-concession-row', function() {
                const year = $(this).data('year');
                const rowCount = $(`#${year}-year-concession .concession-row`).length;
                if (rowCount > 1) {
                    $(this).closest('tr.concession-row').remove();
                }
            });

            // Previous button handler
            $('#fees_concession_info_faculty_form_prev_btn').on('click', async function() {
                showComponentLoading(1);
                const params = {
                    action: 'add',
                    route: 'faculty',
                    type: 'fees',
                    tab: 'fees_details'
                };
                const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                const newUrl = window.location.origin + window.location.pathname + queryString;
                window.history.pushState(params, '', newUrl);
                await loadUrlBasedOnURL();
                setTimeout(function() {
                    hideComponentLoading();
                }, 100);
            });

            // Next button handler
            $('#fees_concession_info_faculty_form_nxt_btn').on('click', async function(e) {
                showComponentLoading(2)
                e.preventDefault();
                const formData = new FormData($('#student-concession-structure')[0]); // Corrected to reference the form
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/student_concession_structure_info_update.php', ENT_QUOTES, 'UTF-8') ?>',
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
                                type: 'documentupload',
                                tab: 'document'
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