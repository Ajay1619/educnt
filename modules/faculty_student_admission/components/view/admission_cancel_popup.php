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
        <button class="popup-close-btn" onclick="closePopup()">✖</button>

        <!-- Popup Header -->
        <div class="popup-header">
            <h2>Confirm Removal of Student</h2>
        </div>

        <!-- Popup Content -->
        <div class="popup-content">
            <p class="popup-quotes">"Are you sure you want to remove this student from the admission list? This action cannot be undone. 🚫"</p>
        </div>

        <!-- Popup Footer -->
        <div class="popup-footer">
        <input type="hidden" name="student_id" id="student-id" value="<?= $admission_single_id?>">
            <button class="particulars primary" id="popup-close-btn" >No, Keep Student ❌</button>
            <button class="particulars primary" onclick="removeStudent()">Yes, Remove ✅</button>
        </div>
    </div>
</div>


   
    <script>
       
// console.log(studentId+"hiiii");
// Log the value to the console

    function removeStudent(event) {
        // e.preventDefault(); // Prevent the default form submission behavior

    // Get the form element
    
    var studentId = document.getElementById('student-id').value;
    // Perform AJAX request
    $.ajax({
    type: 'POST',
    url: '<?= MODULES . '/faculty_student_admission/ajax/student_admission_decline.php' ?>',
    headers: {
        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
    },
    data: { 'studentId': studentId }, // Pass the correct variable
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

    $(document).ready(function () {
      
        // Fetch new student account code
        // fetch_new_student_account_code();

        // Trigger fetch_academic_batch on admission type focus or click
        $('#admission-dummy-create').on('click focus', async function () {
            await fetch_academic_batch($(this));
        });

        // Handle file input click
        $('.browse-text').on('click', function () {
            $(this).parent().siblings('.file-input').trigger('click');
        });

        // Initialize bulmaCalendar
        const calendars = bulmaCalendar.attach('#date-of-admission, #academic_year', {
            type: 'date',
            dateFormat: '<?= BULMA_DATE_FORMAT; ?>',
            validateLabel: "",
        });

        // Update input on date select
        calendars.forEach(calendar => {
            calendar.on('select', function (datepicker) {
                document.querySelector('#date-of-admission, #academic_year').value = datepicker.data.value();
            });
        });

        // Close popup on overlay click
       
        $(window).on('click', function (event) {
            if (event.target == document.getElementById('student-admission-popup-overlay')) {
                $('#student-admission-popup').html('');
            }
        });
        // Clear popup on close button click
        $('#popup-close-btn').on('click', function () {
            $('#student-admission-popup').html('');
            $('#student-admission-popup-overlay').fadeOut();
        });
        // parent_account_creation();
    });
</script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
