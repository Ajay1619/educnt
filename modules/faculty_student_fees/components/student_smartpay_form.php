<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="main-content-card action-box">
        <div class="main-content-card-header">
            <h2 class="action-title">Student Fees Payment</h2>
        </div>
        <div class="main-content-card-body">
            <form id="student-fees-payment-form" method="POST">
                <div class="row">
                    <div class="col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <div class="card">
                            <div class="Payment-form-accordion">
                                <div class="row">
                                    <div class="col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                        <div class="input-container">
                                            <input type="text" id="student-name" name="student_name" placeholder=" " required>
                                            <label class="input-label" for="student-name">Enter The Student Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="action-box-content" id="action-box-content">
                                    <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/gifs/Currency.gif' ?>" alt="">
                                    <p class="action-text">
                                        Enter The Student Name To View The Fees Structure.
                                    </p>
                                    <div class="action-hint">
                                        An investment in knowledge pays the best interest.
                                    </div>
                                </div>
                                <div class="student-fees-payment-form" style="display: none;">
                                    <div class='row'>
                                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                            <div class='section-header-title text-left'>Registration Number :
                                                <span class='text-light' id='student-register-number'>20TD0869</span>
                                            </div>
                                        </div>
                                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                            <div class='section-header-title text-right'>Course :
                                                <span class='text-light' id='student-course'>B-tech CSE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                                            <div class='section-header-title text-left'>Wallet Balance :
                                                <span class='text-light' id='student-wallet-balance'>0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <?php for ($year = 1; $year <= 4; $year++): ?>
                                                <div class="accordion">
                                                    <div class="accordion-item">
                                                        <div class="accordion-header">
                                                            <?= $year . ($year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th'))) ?> Year Fees
                                                            <span class="accordion-icon">+</span>
                                                        </div>
                                                        <div class="accordion-content">
                                                            <table class="portal-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Fee Category</th>
                                                                        <th>Total Amount</th>
                                                                        <th>Concession Amount</th>
                                                                        <th>Paid</th>
                                                                        <th>Pending</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Tuition Fee</td>
                                                                        <td>50000</td>
                                                                        <td>30000</td>
                                                                        <td>30000</td>
                                                                        <td>20000</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Hostel Fee</td>
                                                                        <td>30000</td>
                                                                        <td>15000</td>
                                                                        <td>15000</td>
                                                                        <td>15000</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Vertical line -->
                    <div class="vertical-line"></div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="payment-input-section">
                            <h3>Billing</h3>
                        </div>
                        <div class="row">
                            <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-container dropdown-container">
                                    <input type="text" id="payment-student-year-dummy" class="auto dropdown-input" placeholder=" " required>
                                    <label class="input-label" for="payment-student-year-dummy">Select Year</label>
                                    <input type="hidden" name="payment_student_year" id="payment-student-year">
                                    <span class="dropdown-arrow">⌄</span>
                                    <div class="dropdown-suggestions" id="payment-student-years-suggestions"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-container dropdown-container">
                                    <input type="text" id="student-payment-fees-category-dummy" class="auto dropdown-input" placeholder=" " required>
                                    <label class="input-label" for="student-payment-fees-category-dummy">Select Fees Category</label>
                                    <input type="hidden" name="student_payment_fees_category" id="student-payment-fees-category">
                                    <span class="dropdown-arrow">⌄</span>
                                    <div class="dropdown-suggestions" id="student-payment-fees-categories-suggestions"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-container">
                                    <input type="number" id="student-payment-fees-amount" name="student_payment_fees_amount" placeholder=" " min="0" required>
                                    <label class="input-label" for="student-payment-fees-amount">Enter Fees Amount</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                                <div class='section-header-title text-left'>Pending Fees :
                                    <span class='text-light' id='student-pending-fees'>20000</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                                <div class='section-header-title text-left'>Balance :
                                    <span class='text-light' id='student-payment-balance'>0</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-container dropdown-container">
                                    <input type="text" id="student-payment-method-dummy" class="auto dropdown-input" placeholder=" " required>
                                    <label class="input-label" for="student-payment-method-dummy">Select Payment Method</label>
                                    <input type="hidden" name="student_payment_method" id="student-payment-method">
                                    <span class="dropdown-arrow">⌄</span>
                                    <div class="dropdown-suggestions" id="student-payment-method-suggestions"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-container">
                                    <input type="text" id="student-payment-fees-remarks" name="student_payment_fees_remarks" placeholder=" " required>
                                    <label class="input-label" for="student-payment-fees-remarks">Enter Remarks</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="full-button">Submit Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() { // IIFE to isolate scope



            $(document).ready(function() {
                // Initial state
                $("#action-box-content").show();
                $('#student-name').focus();



                // Initialize accordions
                initializeAccordions();
            });

            $('#student-name').on('blur', function() {
                setTimeout(() => {
                    const studentName = $('#student-name').val().trim();
                    if (studentName) {
                        showComponentLoading();
                        loadStudentFeesForm(studentName);
                        setTimeout(hideComponentLoading, 100);
                    } else {
                        $("#action-box-content").show();
                        $(".student-fees-payment-form").hide();
                    }
                }, 100);
            });

            function initializeAccordions() {
                const $accordionHeaders = $('.accordion-header');
                if (!$accordionHeaders.length) return;

                $accordionHeaders.off('click').on('click', function() {
                    const $header = $(this);
                    const $content = $header.next('.accordion-content');
                    const $icon = $header.find('.accordion-icon');
                    const isActive = $header.hasClass('active');

                    $('.accordion-content').not($content).slideUp(300).prev().removeClass('active').find('.accordion-icon').text('+');

                    $header.toggleClass('active', !isActive);
                    if (isActive) {
                        $content.slideUp(300, () => $icon.text('+'));
                    } else {
                        $content.slideDown(300, () => $icon.text('+'));
                    }
                });
            }

            // Placeholder for loadStudentFeesForm
            function loadStudentFeesForm(studentName) {
                $("#action-box-content").hide();
                $(".student-fees-payment-form").show();
                // Add logic to fetch and populate student data if needed
            }
        })();
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>