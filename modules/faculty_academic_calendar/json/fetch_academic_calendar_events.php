<?php
function generateCalendar($month, $year, $events)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
    $currentMonthName = date('F', strtotime("$year-$month-01"));

    $calendar = "<div class='calendar-header'>";
    $calendar .= "<button id='prev-month'>&lt;</button>";
    $calendar .= "<span>$currentMonthName $year</span>";
    $calendar .= "<button id='next-month'>&gt;</button>";
    $calendar .= "</div>";

    $calendar .= "<div class='calendar-grid'>";
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='day-name'>$day</div>";
    }

    // Empty cells for days before the start of the month
    for ($i = 0; $i < $firstDayOfMonth; $i++) {
        $calendar .= "<div class='calendar-day empty'></div>";
    }

    // Generate calendar days
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = "$year-$month-$day";
        $eventList = "";
        foreach ($events as $event) {
            if ($event['event_start_date'] <= $date && $event['event_end_date'] >= $date) {
                $eventList .= "<div class='event'>{$event['event_name']}</div>";
            }
        }
        $calendar .= "<div class='calendar-day' data-date='$date'>$day<div class='event-list'>$eventList</div></div>";
    }

    $calendar .= "</div>";
    return $calendar;
}

