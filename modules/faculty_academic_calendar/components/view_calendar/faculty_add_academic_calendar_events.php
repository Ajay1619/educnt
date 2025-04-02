<?php
include_once('../../../../config/sparrow.php');

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

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
    <div class="main-content-card action-box">
    <div class="main-content-card-header">
            <h2 class="action-title">Add Events</h2>
        </div>
    <form method="POST" id="addEventForm" class="form" >
        <div class="form-container">
            
            <div class="row">
                <!-- Event Name -->
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container">
                        <input type="text" id="eventName" name="eventName" placeholder=" " required>
                        <label class="input-label" for="eventName">Event Name</label>
                    </div>
                </div>

                <!-- Event Description -->
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container">
                        <input type="text" id="eventDescription" name="eventDescription" placeholder=" " required>
                        <label class="input-label" for="eventDescription">Event Description</label>
                    </div>
                </div>
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container date">
                        <input type="date" value="" class="bulmaCalender" id="eventStartDate" name="eventStartDate" placeholder="dd-MM-yyyy" required aria-required="true">
                        <label class="input-label " for="eventStartDate">Event Start Date</label>
                    </div>
                </div>
                <!-- Event Start Date
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="input-container">
                        <input type="date" id="eventStartDate" name="eventStartDate" placeholder=" " required>
                        <label class="input-label" for="eventStartDate">Event Start Date</label>
                    </div>
                </div> -->
            </div>
            <div class="row">
            <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 ">
                    <div class="input-container date">
                        <input type="date" value="" class="bulmaCalender" id="eventEndDate" name="eventEndDate" placeholder="dd-MM-yyyy" required aria-required="true">
                        <label class="input-label " for="eventEndDate">Event End Date</label>
                    </div>
                </div>

                <!-- Event Type -->
                <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="event-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto event-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Event</label>
                            <input type="hidden" name="event_filter" class="event-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
            </div>

            <!-- Submit Button -->
            <div class="col col-12">
                <button type="submit" class="full-button">Add Event</button>
            </div>
        </div>
    </form>
    </div>

    <script>
    $(document).ready(async function() {
        // Function to dynamically position the Bulma Calendar
        const adjustCalendarPosition = (calendarId) => {
            const inputField = document.querySelector(calendarId);
            //const calendarContainer = document.querySelector('.bulma-calendar');

            if (!inputField || !calendarContainer) return;

            // Get input field and viewport dimensions
            const inputRect = inputField.getBoundingClientRect();
            const calendarHeight = calendarContainer.offsetHeight;
            const spaceBelow = window.innerHeight - inputRect.bottom;
            const spaceAbove = inputRect.top;

            // Adjust position based on available space
            if (spaceBelow < calendarHeight && spaceAbove > calendarHeight) {
                // Position above the input field
                calendarContainer.style.position = 'absolute';
                calendarContainer.style.top = `${inputRect.top - calendarHeight}px`;
                calendarContainer.style.left = `${inputRect.left}px`;
            } else {
                // Default: position below the input field
                calendarContainer.style.position = 'absolute';
                calendarContainer.style.top = `${inputRect.bottom}px`;
                calendarContainer.style.left = `${inputRect.left}px`;
            }
        };

//         $('#addEventForm').on('submit', function(e) {
//     e.preventDefault();
    
//     // Get form values
    
    
//     // Create FormData object and populate it
//     const formData = new FormData(); 
    
//     // Log data for debugging (optional) 
    
//     $.ajax({
//         type: 'POST',
//         url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/ajax/add_academic_calendar_events.php', ENT_QUOTES, 'UTF-8') ?>',
//         headers: {
//             'X-CSRF-Token': '<?= $csrf_token ?>'
//         },
//         data: formData,
//         processData: false, // Prevent jQuery from processing the data
//         contentType: false, // Let the browser set the content type (multipart/form-data)
//         success: function(response) {
//             let parsedResponse;
//             try {
//                 parsedResponse = JSON.parse(response);
//             } catch (err) {
//                 console.error('Failed to parse response:', response);
//                 showToast('error', 'Invalid server response');
//                 return;
//             }
            
//             if (parsedResponse.code === 200) {
//                 showToast('success', parsedResponse.message);
//                 $('#addEventForm')[0].reset();
//             } else {
//                 showToast('error', parsedResponse.message);
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error('AJAX error:', status, error);
//             showToast('error', 'An error occurred while adding the event');
//         }
//     });
// });

$('#addEventForm').on('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/faculty_academic_calendar/ajax/add_academic_calendar_events.php' ?>',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                'X-Requested-Path': window.location.pathname + window.location.search
            },
            success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            window.location.reload();
                        } else {
                            showToast(response.status, response.message);
                        }
                        // location.reload();
                    },
        });
        
        const parsedResponse = JSON.parse(response);
        showToast(parsedResponse.status, parsedResponse.message);
        
    } catch (error) {
        showToast('error', 'Something went wrong. Please try again later.');
    }
});

        // Attach event listeners to dynamically adjust the calendar's position
        $('.event-filter-dummy').on('click focus', function() {
                fetch_events($(this));
            });
         

        // Set up an event listener to update the input field when a date is selected
       
    });
</script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>