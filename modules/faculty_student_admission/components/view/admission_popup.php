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
    $admission_single_id = isset($_GET['studentId']) ? sanitizeInput($_GET['studentId'], 'int') : 0;
    // echo $admission_single_id;
?>

    <div class="popup-overlay" id="student-admission-popup-overlay">
        <div class="alert-popup no-scroll">
            <!-- Close Button -->

            <!-- Popup Header -->
            <div class="popup-header">
                <h2>Student Account Creation </h2>
            </div>

            <!-- Popup Content -->
            <div class="popup-content">
                <p class="popup-quotes">"With great power comes great responsibility... and an awesome student account. 🎓📋"</p>
                <form id="student-account-creation" method="post">
                    <div class="input-container dropdown-container">
                        <input type="text" id="admission-dummy-create" name="admissions" class="auto dropdown-input" placeholder=" " readonly required>
                        <label class="input-label" for="admission-dummy-create">Select Your admission Type</label>
                        <input type="hidden" name="admission_type" id="admission-batch">
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions" id="admission_batch_suggestion"></div>
                    </div>
                    <div class="input-container">
                        <input type="text" id="student-username" class="student-username" name="student_username" placeholder=" " readonly>
                        <label class="input-label" for="student-username">Student Username</label>
                    </div>
                    <!-- <input type="hidden" name="role_id" id="role-id">
                    <input type="hidden" name="account_number" id="account-number">-->
                    <input type="hidden" name="account_id" id="account-id"> 
                    
                    <input type="hidden" name="student_id" id="student-id" value="<?= $admission_single_id?>">

                </form>
            </div>

            <!-- Popup Footer -->
            <div class="popup-footer">
                <button class="particulars primary"  onclick="submitForm()">Submit</button>
                <button class="particulars primary" id="popup-close-btn" onclick="closePopup()">Cancel</button>
            </div>
        </div>
    </div>




  
    <script>
         var studentId = document.getElementById('student-id').value;

// Log the value to the console

function closePopup(event) {
    const form = document.getElementById('student-account-creation');
    const formData = new FormData(form);
    $.ajax({
        type: 'POST',
        url: '<?= MODULES . '/faculty_student_admission/ajax/delete_new_account_data.php' ?>',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
        },
        processData: false, // Prevent jQuery from processing data
        contentType: false, // Prevent jQuery from setting content-type header
        success: function (response) {
            try {
                response = JSON.parse(response);
                if (response.code == 200) {
                    showToast(response.status, response.message);
                    // location.reload();
                    $('#student-admission-popup').html('');
                    $('#student-admission-popup-overlay').fadeOut();
                    
                } else {
                    showToast(response.status, response.message);
                }
            } catch (e) {
                showToast('error', 'Invalid server response.');
            }
        },
        error: function () {
            showToast('error', 'Something went wrong. Please try again later.');
        }
    });

}
    function submitForm(event) {
        // e.preventDefault(); // Prevent the default form submission behavior

    // Get the form element
    const form = document.getElementById('student-account-creation'); // Corrected id

    if (!form) {
        console.error("Form with id 'student-account-creation' not found.");
        return;
    }

    const formData = new FormData(form);

    // Perform AJAX request
    $.ajax({
        type: 'POST',
        url: '<?= MODULES . '/faculty_student_admission/ajax/student_admission_confirm_update.php' ?>',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
        },
        processData: false, // Prevent jQuery from processing data
        contentType: false, // Prevent jQuery from setting content-type header
        success: function (response) {
            try {
                response = JSON.parse(response);
                if (response.code == 200) {
                    showToast(response.status, response.message);
                    // location.reload();
                    $('#student-admission-popup').html('');
                    $('#student-admission-popup-overlay').fadeOut();
                    parent_account_creation(studentId);
                } else {
                    showToast(response.status, response.message);
                }
            } catch (e) {
                showToast('error', 'Invalid server response.');
            }
        },
        error: function () {
            showToast('error', 'Something went wrong. Please try again later.');
        }
    });
}

    $(document).ready(function () {
      
        // Fetch new student account code
        fetch_new_student_account_code();

        // Trigger fetch_academic_batch on admission type focus or click
        $('#admission-dummy-create').on('click focus', async function () {
            await fetch_academic_batch($(this));
        });

        // Handle file input click
        $('.browse-text').on('click', function () {
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
            calendar.on('select', function (datepicker) {
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
        // $('#popup-close-btn').on('click', function () {
        //     $('#student-admission-popup').html('');
        //     $('#student-admission-popup-overlay').fadeOut();
        // });
        // parent_account_creation();
    });
</script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
