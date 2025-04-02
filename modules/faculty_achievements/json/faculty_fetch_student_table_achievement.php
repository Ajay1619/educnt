<?php
include_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Read DataTables parameters
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';

        $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int') : 0;
        $achievement_type = isset($_POST['achievement_type']) ? sanitizeInput($_POST['achievement_type'], 'int') : 0;

        // Define column names for sorting (should match DataTables column indexes)
        $columns = ['faculty_achievements_id', 'achievement_title', 'achievement_date', 'achievement_venue', 'achievement_status'];

        // Get the column name to sort by
        $sort_column = ($order_column >= 0 && $order_column < count($columns)) ? $columns[$order_column] : 'faculty_achievements_id';

        // Prepare input parameters for the procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $faculty_id],
            ['type' => 'i', 'value' => $achievement_type],
            ['type' => 'i', 'value' => $logged_login_id],
            ['type' => 'i', 'value' => 1],

        ];
    

        // Call the stored procedure
        $response = callProcedure('fetch_pr_student_achievement_table_data', $inputParams);



if ($response['particulars'][0]['status_code'] == 200) {
    // Extract data
    $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
    $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
    $data = isset($response['data'][0]) ? $response['data'][0] : [];

    // Prepare data for DataTables
    $table_data = [];
    $s_no = $start + 1;

    if (isset($data[0]["achievement_status"])) {
        foreach ($data as $row) {
            // Format data for DataTables
            $status_label = $row['achievement_status'] == 1 ? 'Active' : 'Inactive';
            $status_class = $row['achievement_status'] == 1 ? 'badge-success' : 'badge-danger';

            $status_badge = <<<HTML
            <span class="badge {$status_class}">{$status_label}</span>
            HTML;

            // Action buttons
            $action_svg_1 = GLOBAL_PATH . '/images/svgs/application_icons/download.svg';
            $path = DEV_STUDENT_PATH . 'uploads/student_achievements/'.$row['achievement_document'];

            if ($path != "") {
                $view_button = <<<HTML
                <div class="action-buttons">
                    <a href="{$path}" target="_blank">
                        <img src="{$action_svg_1}" class="action-button">
                    </a>
                </div>
                HTML;
            } else {
                $view_button = '<span class="text-danger">File not found</span>';
            }

            // Add row data
            $table_data[] = [
                'sl_no' => $s_no++,
                'achievement_title' => $row['achievement_title'],
                'achievement_date' => $row['achievement_date'],
                'achievement_venue' => $row['achievement_venue'],
                'student_name' => $row['student_name'],
                'department_name' => $row['dept_short'],
                'action' => $view_button
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

            // Send JSON response
            echo json_encode($result);
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Failed to fetch achievement data.']);
        }
        //   print_r($response);
    } catch (Exception $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => $e->getMessage()]);
    }
}
