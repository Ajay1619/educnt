<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    //Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
    $admission_single_id = 0;
    $admission_single_id = isset($_GET['studentId']) ? sanitizeInput($_GET['studentId'], 'int') : 0;
    echo $admission_single_id;
?>

    <div class="popup-overlay" id="parent-admission-popup-overlay">
        <div class="alert-popup no-scroll">
            <!-- Close Button -->


            <!-- Popup Header -->
            <div class="popup-header">
                <h2>Parent Account Creation</h2>
            </div>

            <!-- Popup Content -->
            <div class="popup-content" id="parent-assign-popup" >
                <p class="popup-quotes">"With great power comes great responsibility... and an awesome parent account. 👨‍👩‍👧‍👦📋"</p>
                <form id="parent-account-creation" method="post">
                    <!-- Parent Account ID -->
                    <div class="input-container dropdown-container">
                        <input type="text" id="relation-dummy-create" name="relations" class=" relation_dummy_create auto dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="relation-dummy-create">Select Student relation Type</label>
                        <input type="hidden" name="relation_type" id="relation-batch">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="relation_batch_suggestion"></div>
                    </div>

                    <!-- Parent First Name -->
                    <div class="input-container">
                        <input type="text" id="parent-user-name" name="parent_user_name" placeholder=" " readonly required>
                        <label class="input-label" for="parent-user-name">Username</label>
                    </div>

                    <div class="input-container">
                        <input type="text" id="parent-first-name" name="parent_first_name" placeholder=" " required>
                        <label class="input-label" for="parent-first-name">First Name</label>
                    </div>


                    <!-- Parent Middle Name -->
                    <div class="input-container">
                        <input type="text" id="parent-middle-name" name="parent_middle_name" placeholder=" ">
                        <label class="input-label" for="parent-middle-name">Middle Name (Optional)</label>
                    </div>

                    <!-- Parent Last Name -->
                    <div class="input-container">
                        <input type="text" id="parent-last-name" name="parent_last_name" placeholder=" ">
                        <label class="input-label" for="parent-last-name">Last Name</label>
                    </div>

                    <!-- Parent Initial -->
                    <div class="input-container">
                        <input type="text" id="parent-initial" name="parent_initial" placeholder=" " required>
                        <label class="input-label" for="parent-initial">Initial</label>
                    </div>

                    <!-- Parent Mobile Number -->
                    <div class="input-container">
                        <input type="tel" id="parent-mobile-number" name="parent_mobile_number" placeholder=" " required pattern="[0-9]{10,15}">
                        <label class="input-label" for="parent-mobile-number">Mobile Number</label>
                    </div>

                    <!-- Parent Email ID -->
                    <div class="input-container">
                        <input type="email" id="parent-email-id" name="parent_email_id" placeholder=" " required>
                        <label class="input-label" for="parent-email-id">Email ID (Optional)</label>
                    </div>
                    <input type="hidden" name="parent_code" id="parent-code">
                    <input type="hidden" name="parent_role" id="parent-role">
                    <input type="hidden" name="parent_type" id="parent-type">
                    <input type="hidden" name="student_id" id="student-id" value="<?= $admission_single_id ?>">

                    <!-- Submit Button -->
                    <!-- <div class="input-container">
                <button type="submit" class="submit-button">Create Parent Account</button>
            </div> -->
                </form>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <button class="particulars primary" onclick="submitParentForm()">Submit</button>
                <button class="particulars primary" onclick="closePopup()">Cancel</button>
            </div>
        </div>
    </div>




    <script>
        //  var studentId = document.getElementById('student-id').value;
        // console.log(studentId+"hi");
        function submitParentForm(event) {
            // e.preventDefault(); // Prevent the default form submission behavior

            // Get the form element
            const form = document.getElementById('parent-account-creation'); // Corrected id

            if (!form) {
                console.error("Form with id 'student-account-creation' not found.");
                return;
            }

            const formData = new FormData(form);

            // Perform AJAX request
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/faculty_student_admission/ajax/student_admission_create_parent.php' ?>',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                },
                processData: false, // Prevent jQuery from processing data
                contentType: false, // Prevent jQuery from setting content-type header
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            // location.reload();
                            $('#parent-admission-popup').html('');
                            $('#parent-admission-popup-overlay').fadeOut();
                            // parent_account_creation();
                            // Reload the page after 1 second
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast(response.status, response.message);
                        }
                    } catch (e) {
                        showToast('error', 'Invalid server response.');
                    }
                },
                error: function() {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }

        $('#parent-first-name').on('blur', function() {
                input_validation($(this));
            });
            $('#parent-middle-name').on('blur', function() {
                input_validation($(this));
            });
            $('#parent-last-name').on('blur', function() {
                input_validation($(this));
            });
            $('#parent-initial').on('blur', function() {
                input_validation($(this));
            });
            $('#parent-mobile-number').on('blur', function() {
                input_validation($(this));
            });
            $('#parent-email-id').on('blur', function() {
                input_validation($(this));
            });
            const input_validation = (element) => {
                const name = element.attr('name');
                const id = element.attr('id');
                const value = element.val();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_profile/ajax/faculty_profile_input_validation.php' ?>',
                    data: {
                        'name': name,
                        'value': value
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code !== 200) {
                            showToast(response.status, response.message);
                            element.addClass(response.status)
                            element.val("");
                        } else {
                            element.removeClass('error');
                            element.addClass(response.status);
                            if (name == 'parent_first_name') {
                                $('#parent-first-name').val(response.data);
                            }
                            if (name == 'parent_middle_name') {
                                $('#parent-middle-name').val(response.data);
                            }
                            if (name == 'parent_last_name') {
                                $('#parent-last-name').val(response.data);
                            }
                            if (name == 'parent_initial') {
                                $('#parent-initial').val(response.data);
                            }
                            if (name == 'parent_mobile_number') {
                                $('#parent-mobile-number').val(response.data);
                            }
                            if (name == 'parent_email_id') {
                                $('#parent-email-id').val(response.data);
                            }

                        }
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            }
        $(document).ready(function() {
            $('.relation_dummy_create').on('click focus', async function() {
                // console.log('student_concession');
                await fetch_parent_relation($(this));
            });
            // Fetch new student account code
            fetch_new_parent_account_code();

            // Trigger fetch_academic_batch on admission type focus or click
            $('#admission-dummy-create').on('click focus', async function() {
                await fetch_academic_batch($(this));
            });

            // Handle file input click
            $('.browse-text').on('click', function() {
                $(this).parent().siblings('.file-input').trigger('click');
            });

            // Initialize bulmaCalendar
            // const calendars = bulmaCalendar.attach('#date-of-admission, #academic_year', {
            //     type: 'date',
            //     dateFormat: '<?= BULMA_DATE_FORMAT; ?>',
            //     validateLabel: "",
            // });

            // Update input on date select
            calendars.forEach(calendar => {
                calendar.on('select', function(datepicker) {
                    document.querySelector('#date-of-admission, #academic_year').value = datepicker.data.value();
                });
            });

            // Close popup on overlay click
            // $(window).on('click', function (event) {
            //     if (event.target == document.getElementById('student-admission-popup-overlay')) {
            //         $('#student-admission-popup').html('');
            //     }
            // });

            // Clear popup on close button click

        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
