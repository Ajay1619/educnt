<?php
include_once('../../../config/sparrow.php');

// Get the year and month from POST or use the current year and month
$year = isset($_POST['Year']) ? sanitizeInput($_POST['Year'], 'int') : date('Y');
$month = isset($_POST['Month']) ? sanitizeInput($_POST['Month'], 'int') : date('n');

// Prepare the stored procedure parameters
$procedure_params = [
    ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
    ['name' => 'Month', 'value' => $month, 'type' => 'i'],
    ['name' => 'Year', 'value' => $year, 'type' => 'i']
];

// Call the procedure to fetch events
$result = callProcedure("fetch_pr_faculty_events", $procedure_params);
if ($result) {
    if ($result['particulars'][0]['status_code'] == 200) {
         $timetable = $result["data"][0]; 
        if (isset($result["data"][1])) {
            $events = $result["data"][1];
        } else {
            $events = "";
        }
        // Events array

        // Functions to handle calendar logic
        function daysInMonth($year, $month)
        {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        function getFirstDayOfMonth($year, $month)
        {
            return date('w', strtotime("$year-$month-01"));
        }

        function formatDate($date)
        {
            return date('d.m.Y', strtotime($date));
        }

        // Generate the calendar
        function generateCalendar($year, $month, $events, $timetable)
        {
            $totalDays = daysInMonth($year, $month);
            $firstDayOfMonth = getFirstDayOfMonth($year, $month);
            $daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            $nxtmonth = ($month == 12) ? 1 : $month + 1;
            $prevmonth = ($month == 1) ? 12 : $month - 1;
            echo '<div class="calendar">';
            echo '<div class="calendar-header">';
            echo '<button class="nav-back" data-month="' . $prevmonth . '" data-year="' . (($month == 1) ? $year - 1 : $year) . '" id="prev-month">&lt;</button>';
            echo '<h3 class="current-month">' . date('F Y', strtotime("$year-$month-01")) . '</h3>';
            // echo '<button class="nav-next" data-month="'.$nxtmonth.'" data-year="'. $retVal = ($month == 12) ? "a" : "b "; .'" id="next-month">&gt;</button>';
            echo '<button class="nav-next" data-month="' . $nxtmonth . '" data-year="' . (($month == 12) ? $year + 1 : $year) . '" id="next-month">&gt;</button>';

            echo '</div>';

            echo '<div class="calendar-grid">';

            // Display days of the week
            foreach ($daysOfWeek as $day) {
                echo '<div class="day-name">' . $day . '</div>';
            }

            // Add empty cells for the first row
            for ($i = 0; $i < $firstDayOfMonth; $i++) {
                echo '<div class="calendar-day empty"></div>';
            }

            // Populate days
            for ($i = 1; $i <= $totalDays; $i++) {
                $currentDay = strtotime("$year-$month-$i");
                $formattedDate = formatDate(date('Y-m-d', $currentDay));
                $dayOfWeek = date('w', $currentDay);
                $dayClass = ($timetable[$dayOfWeek]['timetable_status'] == 2) ? 'holiday' : 'working-day';



                echo '<div class="calendar-day ' . $dayClass . '">' . $i;
                if ($events != "") {
                    $dayEvents = array_filter($events, function ($event) use ($currentDay) {
                        $startDate = strtotime($event['start_date']);
                        $endDate = strtotime($event['end_date']);
                        return $currentDay >= $startDate && $currentDay <= $endDate;
                    });
                    // Display events
                    $eventCount = 0;
                    // print_r($event);
                    foreach ($dayEvents as $event) {
                        if ($eventCount < 3) {
                            // print_r($dayEvents);
                            echo '<div class="event-label"  data-sd="'.$dayEvents[$eventCount]["start_date"].'"  data-name="'.$dayEvents[$eventCount]["name"].'" data-des="'.$dayEvents[$eventCount]["description"].'">' . htmlspecialchars($event['name']) . '</div>';
                             

                            $eventCount++;
                        }
                    }
                    if (count($dayEvents) > 3) {
                        echo '<div class="more-event-labels">+' . (count($dayEvents) - 3) . ' events</div>';
                    }
                }


                echo '</div>';
            }

            echo '</div>'; // Close calendar-grid
            echo '</div>'; // Close calendar
        }

        // Generate the calendar with the events
        generateCalendar($year, $month, $events, $timetable);
?>
        <script>
            $('#next-month').click(function() {

                let month = $('#next-month').data('month');
                let year = $('#next-month').data('year');
                fetch_academic_calendar_view(month, year);
                // Log the value to the console

            });
            $('#prev-month').click(function() {

                let month = $('#prev-month').data('month');
                let year = $('#prev-month').data('year');
                fetch_academic_calendar_view(month, year);
                // Log the value to the console

            });
        </script>
<?php
        exit;
    } else {
        echo json_encode([
            'code' => $result['particulars'][0]['status_code'],
            'status' => $result['particulars'][0]['status'],
            'message' => $result['particulars'][0]['message']
        ]);
        exit;
    }
} else {
    echo json_encode([
        'code' => 500,
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
?>