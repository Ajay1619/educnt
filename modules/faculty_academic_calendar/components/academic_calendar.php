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

  $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
  $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
  <!-- <div class="col col-6 col-lg-9 col-md-9 col-sm-12 col-xs-12">
    <div class="calendar">
      <div class="calendar-header">
        <button class="nav-back" id="prev-month">&lt;</button>
        <h3 class="current-month"></h3>
        <button class="outline_black small" id="list-of-events">Events</button>
        <button class="nav-next" id="next-month">&gt;</button>
      </div>
      <div class="calendar-grid">
        <div class="day-name">Sun</div>
        <div class="day-name">Mon</div>
        <div class="day-name">Tue</div>
        <div class="day-name">Wed</div>
        <div class="day-name">Thu</div>
        <div class="day-name">Fri</div>
        <div class="day-name">Sat</div>
        <!-- Days will be generated dynamically -->
      </div>
    </div>
  </div> -->



  <script src="<?= MODULES . '/faculty_academic_calendar/js/academic_calendar.js' ?>"></script>

  <script>
    // Updated version of the fetch_year_list function
    const fetch_year_list = (element, dayData) => {
      console.log(dayData); // Log dayData to verify the data structure

      return new Promise((resolve, reject) => {
        $.ajax({
          url: '<?= GLOBAL_PATH . '/json/fetch_year_of_study.php' ?>',
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
          },
          data: {
            date: dayData.date, // Pass the selected date (e.g., "2024-11-01")
            day: dayData.day, // Pass the day (e.g., "Mon")
            year: dayData.year // Pass the year (e.g., 2024)
          },
          success: function(response) {
            response = JSON.parse(response);
            // if (response.code === 200) {
            //   const year_list = response.data;
            //   console.log(year_list);

            //   const suggestions = element.siblings(".dropdown-suggestions");
            //   const value = element.siblings(".faculty-year-filter");
            //   showSuggestions(year_list, suggestions, value, element);
            // } else {
            //   showToast(response.status, response.message);
            // }
            resolve(response);
          },
          error: function(error) {
            reject(error);
          }
        });
      });
    };

    $(document).ready(async function () {
    try {
        await academic_calendar();
        initCustomContextMenuForCalendar();

        // Bind event to the Add Event menu item
        
        $('#add-events-popup').on('click', async function () {
          console.log("hello");
          
            try {
                await load_academic_calendar_add_event_popup();
            } catch (error) {
                console.error('Error loading Add Event popup:', error);
            }
        });
    } catch (error) {
        console.error('An error occurred while processing:', error);
    }
});

const load_academic_calendar_add_event_popup = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/academic_calendar_popup.php', ENT_QUOTES, 'UTF-8') ?>',
            headers: {
                'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
            },
            success: function (response) {
                // Inject popup content into the placeholder
                $('#academic-calendar-popup').html(response);

                // Show the popup and bind the close button
                $('#academic-calendar-popup').show();
                $('.popup-close-btn').on('click', function () {
                    $('#academic-calendar-popup').hide().html('');
                });
                resolve();
            },
            error: function (jqXHR) {
                const message =
                    jqXHR.status === 401
                        ? 'Unauthorized access. Please check your credentials.'
                        : 'An error occurred. Please try again.';
                showToast('error', message);
                reject();
            }
        });
    });
};
  </script>

<?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
?>