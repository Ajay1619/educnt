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
        // Read DataTables parameters
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';

        $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int') : 1;
        $faculty_subject_id = isset($_POST['faculty_subject_id']) ? sanitizeInput($_POST['faculty_subject_id'], 'int') : 0;

        $columns = ['faculty_subjects_id', 'attendance_date', 'period_id', 'attendance', 'attendance', 'attendance'];

        if ($order_column >= 0 && $order_column < count($columns)) {
            $sort_column = $columns[$order_column];
        } else {
            $sort_column = 'faculty_subjects_id'; // Default sort column
        }

        // Prepare input parameters for stored procedure or SQL query
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $faculty_subject_id],
            ['type' => 'i', 'value' => $logged_login_id]
        ];

        $response = callProcedure('fetch_pr_subjectwise_attendance_table', $inputParams);
        if ($response['particulars'][0]['status_code'] == 200) {
            $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
            $data = isset($response['data'][0]) ? $response['data'][0] : [];
            if ($type == 1) {
                $table_data = [];
                $s_no = $start + 1;

                if (isset($response['data'][0][0]['Date'])) {
                    foreach ($data as $row) {
                        $row['faculty_subjects_id'] = encrypt_data($row['faculty_subjects_id']);
                        $encrypted_date = encrypt_data($row['Date']);
                        $row['period_id'] = encrypt_data($row['period_id']);
                        $row['Date'] = date(DATE_FORMAT, strtotime($row['Date']));
                        // Status switch checkbox


                        $action_svg_1 = GLOBAL_PATH . '/images/svgs/eye.svg';
                        $action_svg_2 = GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg';
                        $action_svg_3 = GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg';
                        // Action buttons
                        $action_buttons = <<<HTML
                        <div class="action-buttons">
                                <img src="{$action_svg_1}" class="action-button" onclick="load_individual_student_subjectwise_attendance_view_table('{$row['faculty_subjects_id']}','{$encrypted_date}','{$row['period_id']}')">
                                <img src="{$action_svg_2}" class="action-button" onclick="load_attendance_edit_form('{$row['faculty_subjects_id']}','{$encrypted_date}','{$row['period_id']}')">
                                <img src="{$action_svg_3}" class="action-button" onclick="delete_subject_attendance('{$row['faculty_subjects_id']}','{$encrypted_date}','{$row['period_id']}')">
                        </div>
                    HTML;

                        // Add the row data to the table
                        $table_data[] = [
                            's_no' => $s_no++,
                            'attendance_date' => $row['Date'],
                            'period' => $row['Period'],
                            'present_count' => $row['Present_Count'],
                            'absent_count' => $row['Absent_Count'],
                            'percentage' => $row['Attendance_Percentage'],
                            'action' => $action_buttons
                        ];
                    }
                }


                // Prepare the response for DataTables
                $result = [
                    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                    "recordsTotal" => $total_records,
                    "recordsFiltered" => $filtered_records,
                    "data" => $table_data
                ];


                // Send the response as JSON

                echo json_encode($result);
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
