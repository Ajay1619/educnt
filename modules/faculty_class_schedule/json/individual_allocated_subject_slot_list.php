<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];
    try {

        $dept_id = !in_array($logged_role_id, $tertiary_roles) ? $logged_dept_id : 0;
        if (in_array($logged_role_id, $primary_roles) || in_array($logged_role_id, $higher_official)) {
            $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
        }
        $faculty_id = in_array($logged_role_id, $tertiary_roles) ? $logged_user_id : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
        $procedure_params = [

            ['name' => 'user_id', 'value' => $faculty_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ['name' => 'dept_id', 'value' => $dept_id, 'type' => 'i'],
            ['name' => 'year_of_study_id', 'value' => $year_of_study_id, 'type' => 'i'],
            ['name' => 'section_id', 'value' => $section_id, 'type' => 'i'],
        ];
        $result = callProcedure('fetch_pr_allocated_subject_slot_list', $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] == 200) {
                if (isset($result['data'][0]) && is_array($result['data'][0]) && isset($result['data'][0][0]['faculty_subjects_id']) && isset($result['data'][1]) && is_array($result['data'][1])) {
                    $data = $result['data'][0];
                    $days = $result['data'][1];
                    $period_time = [];
                    $table = "";
                    $tableHTML  = "";
                    if (isset($result['data'][2]) && is_array($result['data'][2])) {
                        $period_time = $result['data'][2];
                        // Start building the table string
                        $table = '<table class="portal-table">';

                        // -----------------------
                        // Build the table header
                        // -----------------------
                        $table .= '<thead><tr><th>DAY/PERIOD</th>';

                        // Get the day IDs from the $days array
                        $dayIds = array_column($days, 'day_id');

                        // Determine the maximum number of periods among these days
                        $maxPeriods = 0;
                        foreach ($period_time as $pt) {
                            if (in_array($pt['day_id'], $dayIds) && $pt['period_hour'] > $maxPeriods) {
                                $maxPeriods = $pt['period_hour'];
                            }
                        }

                        // Choose a reference day (the first day that has period definitions) for header labels
                        $referenceDay = null;
                        foreach ($days as $d) {
                            foreach ($period_time as $pt) {
                                if ($pt['day_id'] == $d['day_id']) {
                                    $referenceDay = $d['day_id'];
                                    break;
                                }
                            }
                            if ($referenceDay !== null) {
                                break;
                            }
                        }

                        // Build an array of header labels (using period titles) from the reference day
                        $headers = [];
                        if ($referenceDay !== null) {
                            foreach ($period_time as $pt) {
                                if ($pt['day_id'] == $referenceDay) {
                                    $headers[$pt['period_hour']] = $pt['period_title'];
                                }
                            }
                            ksort($headers); // sort by period_hour
                        }

                        // Create header columns for each period slot (from 1 to $maxPeriods)
                        for ($i = 1; $i <= $maxPeriods; $i++) {
                            // If a header label exists for this period slot, use it; otherwise, just use the period number.
                            $headerTitle = isset($headers[$i]) ? $headers[$i] : $i;
                            $table .= '<th>' . htmlspecialchars($headerTitle) . '</th>';
                        }
                        $table .= '</tr></thead>';

                        // -----------------------
                        // Build the table body
                        // -----------------------
                        $table .= '<tbody>';

                        // Preprocess $data to create a lookup by day and period_hour for easy access
                        $schedule = [];
                        foreach ($data as $record) {
                            $day = $record['day_id'];
                            $period = $record['period_hour'];
                            $schedule[$day][$period] = $record;
                        }

                        // Loop through each day from the $days array to create a row for each day
                        foreach ($days as $day) {
                            $table .= '<tr>';
                            // First column: Day name (converted to uppercase)
                            $table .= '<td  class="portal-background white-text text-center">' . strtoupper($day['day_title']) . '</td>';

                            // For each period slot (from 1 to maximum periods)
                            for ($i = 1; $i <= $maxPeriods; $i++) {
                                $table .= '<td>';
                                // Check if there is a scheduled class for this day and period
                                if (isset($schedule[$day['day_id']][$i])) {
                                    $rec = $schedule[$day['day_id']][$i];
                                    // Display the subject short name and period time information
                                    $table .= htmlspecialchars($rec['subject_short_name']);
                                    $table .= '<br><p class="alert alert-info m-0 text-xsm">' . htmlspecialchars($rec['period_time']) . '</p>';
                                }
                                $table .= '</td>';
                            }
                            $table .= '</tr>';
                        }

                        $table .= '</tbody></table>';
                    }

                    $nestedData = [];
                    $subjectSummary = [];
                    $subjectTypes = [
                        1 => 'Theory',
                        2 => 'Practical',
                        3 => 'Projects',
                        4 => 'Extra Curricular'
                    ];

                    foreach ($data as $entry) {
                        $dayId = $entry['day_id'];
                        $periodId = $entry['period_id'];
                        $subjectId = $entry['faculty_subjects_id'];

                        // Group by days and periods
                        if (!isset($nestedData[$dayId])) {
                            $nestedData[$dayId] = [
                                'day_id' => $entry['day_id'],
                                'day_title' => $entry['day_title'],
                                'room_name' => $entry['room_name'],
                                'room_number' => $entry['room_number'],
                                'section_title' => $entry['section_title'],
                                'year_of_study_title' => $entry['year_of_study_title'],
                                'periods' => []
                            ];
                        }

                        $nestedData[$dayId]['periods'][$periodId] = [
                            'period_title' => $entry['period_title'],
                            'period_time' => $entry['period_time'],
                            'period_hour' => $entry['period_hour'],
                            'timetable_status' => $entry['timetable_status'],
                            'timetable_id' => $entry['timetable_id'],
                            'faculty_subjects_id' => $entry['faculty_subjects_id'],
                            'subject_name' => $entry['subject_name'],
                            'subject_code' => $entry['subject_code'],
                            'subject_short_name' => $entry['subject_short_name']
                        ];

                        // Group by subject and count periods
                        if (!isset($subjectSummary[$subjectId])) {
                            $subjectSummary[$subjectId] = [
                                'subject_code' => $entry['subject_code'],
                                'subject_name' => $entry['subject_name'],
                                'faculty_name' => $entry['faculty_name'],
                                'subject_type_id' => $entry['subject_type'],
                                'subject_type' => $subjectTypes[$entry['subject_type']],
                                'year_of_study_title' => $entry['year_of_study_title'],
                                'section_title' => $entry['section_title'],
                                'dept_short_name' => $entry['dept_short_name'],
                                'no_of_periods' => 0,
                                'alloted_room' => $entry['room_name'] . ' (' . $entry['room_number'] . ')'
                            ];
                        }

                        $subjectSummary[$subjectId]['no_of_periods']++;
                    }

                    // Sorting subjects based on subject_type and then subject_code
                    usort($subjectSummary, function ($a, $b) use ($subjectTypes) {
                        $typeComparison = array_search($a['subject_type'], $subjectTypes) <=> array_search($b['subject_type'], $subjectTypes);

                        if ($typeComparison === 0) {
                            return strcmp($a['subject_code'], $b['subject_code']);
                        }

                        return $typeComparison;
                    });



                    if (isset($result['data'][2]) && is_array($result['data'][2])) {
                        $tableHTML = '<table class="portal-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Faculty Name</th>
                                <th>Year Of Study</th>
                                <th>Section</th>
                                <th>Number Of Periods</th>
                                <th>Room Allocated</th>
                            </tr>
                        </thead>
                        <tbody>';

                        $subjectGroups = [];
                        foreach ($subjectSummary as $subject) {
                            $subjectGroups[$subject['subject_type']][] = $subject;
                        }

                        foreach ($subjectGroups as $type => $subjects) {
                            $tableHTML .= '<tr>
                                <td colspan="7" class="portal-background">' . htmlspecialchars($type) . '</td>
                            </tr>';

                            foreach ($subjects as $subject) {
                                $tableHTML .= '<tr>
                                    <td>' . htmlspecialchars($subject['subject_code']) . '</td>
                                    <td>' . htmlspecialchars($subject['subject_name']) . '</td>
                                    <td>' . htmlspecialchars($subject['faculty_name']) . '</td>
                                    <td>' . htmlspecialchars($subject['year_of_study_title']) . '</td>
                                    <td>' . htmlspecialchars($subject['section_title']) . '</td>
                                    <td>' . htmlspecialchars($subject['no_of_periods']) . '</td>
                                    <td>' . htmlspecialchars($subject['alloted_room']) . '</td>
                                </tr>';
                            }
                        }

                        $tableHTML .= '</tbody></table>';
                    }

                    echo json_encode([
                        'code' => $result['particulars'][0]['status_code'],
                        'status' => $result['particulars'][0]['status'],
                        'message' => $result['particulars'][0]['message'],
                        'data' => [$nestedData],
                        'days' => $days,
                        'table' => $table,
                        'tableHTML' => $tableHTML,
                        'subject_summary' => array_values($subjectSummary) // Reset indexes
                    ]);

                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
                    exit;
                }
            } else {
                echo json_encode([
                    'code' => $result['particulars'][0]['status_code'],
                    'status' => $result['particulars'][0]['status'],
                    'message' => $result['particulars'][0]['message']
                ]);
                exit;
            }
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
