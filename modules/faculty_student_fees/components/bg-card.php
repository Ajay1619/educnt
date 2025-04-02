<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
    // print_r($faculty_page_access_data);
?>
    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-add-button" id="faculty-student-smartpay-button">SmartPay</button>
                        <button class="outline bg-card-view-button" id="faculty-student-feescope-button">FeeScope</button>
                        <button class="outline bg-card-view-credit-button" id="faculty-student-fees-wallet-button">Wallets</button>
                        <button class="outline bg-card-add-fines-button" id="faculty-student-fees-fines-button">Fines</button>
                        <button class="outline bg-card-back-button" id="bg-card-back-button">Back</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <?php if (in_array($logged_role_id, $primary_roles) || in_array($logged_role_id, $higher_official)) { ?>
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="dept-filter">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto dept-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="dept_filter" class="dept-filter" value="0">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto year-of-study-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                            <label class="input-label">Select The Year Of Study</label>
                            <input type="hidden" name="year_of_study_filter" class="year-of-study-filter" id="year-of-study-filter" value="0">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="section-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto section-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                            <label class="input-label">Select The Section</label>
                            <input type="hidden" name="section_filter" class="section-filter" value="0">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <!-- <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="fees-category-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto fees-category-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                            <label class="input-label">Select The Fees Category</label>
                            <input type="hidden" name="fees_category_filter" class="fees-category-filter" value="0">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            try {


                $('#faculty-student-smartpay-button').on('click', async function() {

                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'add',
                            route: 'faculty',
                            type: 'smartpay',
                        });
                        await load_main_components();
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

                $('#faculty-student-fees-wallet-button').on('click', async function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const route = urlParams.get('route');
                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'add',
                            route: route,
                            type: 'wallet',
                            tab: 'credit'
                        });
                        await load_main_components();
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
                $('#faculty-student-fees-fines-button').on('click', async function() {

                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'add',
                            route: 'faculty',
                            type: 'fine',
                            tab: 'charge'
                        });
                        await load_main_components();
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

                $('#faculty-student-feescope-button').on('click', async function() {

                    try {
                        showComponentLoading()
                        updateUrl({
                            action: 'view',
                            route: 'faculty',
                            type: 'overall',
                        });
                        await load_main_components();
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
                $('.dept-filter-dummy').on('click', function() {
                    fetch_dept_list($(this));
                    if ($(".dept-filter").val() == 0 && $(".dept-filter").val() == null && $(".dept-filter").val() == undefined) {
                        $(".year-of-study-filter-dummy").val("");
                        $(".year-of-study-filter").val(0);
                        $(".section-filter-dummy").val("");
                        $(".section-filter").val(0);
                    }
                });
                $('.dept-filter').on('change', function() {
                    $(".year-of-study-filter-dummy").val("");
                    $(".year-of-study-filter").val(0);
                    $('.section-filter-dummy').val("");
                    $('.section-filter').val(0);
                });
                $('.year-of-study-filter-dummy').on('click', function() {

                    if ([...<?= json_encode($primary_roles) ?>, ...<?= json_encode($higher_official) ?>].includes(<?= $logged_role_id ?>)) {

                        fetch_year_list($(this), $(".dept-filter").val());
                    } else {

                        fetch_year_list($(this), <?= $logged_dept_id ?>);
                    }
                    if ($(".year-of-study-filter").val() == 0 && $(".year-of-study-filter").val() == null && $(".year-of-study-filter").val() == undefined) {
                        $(".year-of-study-filter-dummy").val("");
                        $(".year-of-study-filter").val(0);
                        $(".section-filter-dummy").val("");
                        $(".section-filter").val(0);
                    }
                });
                $('.year-of-study-filter').on('change', function() {
                    $('.section-filter-dummy').val("");
                    $('.section-filter').val(0);
                });

                $('.section-filter-dummy').on('click', function() {
                    fetch_section_list($(this), $(".year-of-study-filter").val());
                });

                $('.section-filter-dummy').on('blur', function() {
                    setTimeout(function() {
                        if ($(".section-filter").val() != 0 && $(".section-filter").val() != null && $(".section-filter").val() != undefined) {
                            load_student_wallet_transactions_table($(".dept-filter").val(), $(".year-of-study-filter").val(), $(".section-filter").val());
                        }
                    }, 200)

                });

                // Fees Category Dropdown Handler
                //             $('.fees-category-filter-dummy').on('click focus', function() {
                //                 fetch_fees_category_list($(this));
                //             });
                //             $('.fees-category-filter-dummy').on('blur', async function() {
                //                 showComponentLoading()
                //                 setTimeout(() => {
                //                     const urlParams = new URLSearchParams(window.location.search);
                //                     const type = urlParams.get('type'); // e.g., 'add', 'edit'
                //                     if (type == '') {
                //                         $('#fees-category-filter').show();

                //                     } else if (type == 'wallet') {
                //                             $('#fees-category-filter').hide();

                //                     }
                //                 }, 100);
                //                 setTimeout(function() {
                //                     hideComponentLoading(); // Delay hiding loading by 1 second
                //                 }, 100)
                //             });
                //             // Function to fetch and populate fees category list
                //             function fetch_fees_category_list(element) {
                //                 // Sample fees categories (you might want to fetch this from an API)
                //                 const feesCategories = [{
                //                         id: 1,
                //                         name: 'Tuition Fees'
                //                     },
                //                     {
                //                         id: 2,
                //                         name: 'Hostel Fees'
                //                     },
                //                     {
                //                         id: 3,
                //                         name: 'Transport Fees'
                //                     },
                //                     {
                //                         id: 4,
                //                         name: 'Exam Fees'
                //                     },
                //                     {
                //                         id: 5,
                //                         name: 'Miscellaneous'
                //                     }
                //                 ];

                //                 // Or if you prefer to fetch from an API, uncomment this:
                //                 /*
                //                 $.ajax({
                //                     url: '/api/get-fees-categories', // Replace with your actual API endpoint
                //                     method: 'GET',
                //                     success: function(response) {
                //                         const feesCategories = response.data;
                //                         populateFeesDropdown(feesCategories);
                //                     },
                //                     error: function(error) {
                //                         console.error('Error fetching fees categories:', error);
                //                     }
                //                 });
                //                 */

                //                 // Populate the dropdown
                //                 const dropdown = element.siblings('.dropdown-suggestions');
                //                 dropdown.empty();

                //                 // Add default "All Categories" option
                //                 dropdown.append(`
                //     <div class="dropdown-item" data-value="0">All Categories</div>
                // `);

                //                 // Add fees categories
                //                 feesCategories.forEach(category => {
                //                     dropdown.append(`
                //         <div class="dropdown-item" data-value="${category.id}">${category.name}</div>
                //     `);
                //                 });

                //                 // Show dropdown
                //                 dropdown.show();

                //                 // Handle selection
                //                 dropdown.find('.dropdown-item').on('click', function() {
                //                     const selectedValue = $(this).data('value');
                //                     const selectedText = $(this).text();

                //                     element.val(selectedText);
                //                     element.siblings('.fees-category-filter').val(selectedValue);
                //                     dropdown.hide();
                //                 });

                //                 // Hide dropdown when clicking outside
                //                 $(document).on('click', function(e) {
                //                     if (!element.parent().is(e.target) && element.parent().has(e.target).length === 0) {
                //                         dropdown.hide();
                //                     }
                //                 });
                //             }
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>