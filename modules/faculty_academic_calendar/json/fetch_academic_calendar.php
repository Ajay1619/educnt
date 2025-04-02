<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX POST request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

    try {
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = $_POST['search']['value'] ?? '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = (isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc'])) ? $_POST['order'][0]['dir'] : 'asc';
        $event_type = isset($_POST['event_type']) ? intval($_POST['event_type']) : 0;

        // Define column names for sorting (should match DataTables column indexes)
        $columns = ['event_name', 'start_date', 'event_type'];
        $sort_column = $columns[$order_column] ?? 'student_id';

        // Prepare procedure parameters
        $procedure_params = [
            ['type' => 'i', 'value' => $event_type],
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $logged_login_id]
        ];

        $result = callProcedure("fetch_pr_faculty_table_events", $procedure_params);


        if ($result['particulars'][0]['status_code'] == 200) {
            $total_records = intval($result['data'][1][0]['total_records'] ?? 0);
            $filtered_records = intval($result['data'][1][0]['filtered_records'] ?? 0);
            $data = $result['data'][0] ?? [];

            $table_data = [];
            $s_no = $start + 1;
            if (isset($data[0]['event_id'])) {
                foreach ($data as $row) {

                    $action_buttons = "<div class='action-buttons'>
                    <img src='" . GLOBAL_PATH . "/images/svgs/eye.svg' class='action-button' 
                         onclick='view_individual_student_admission(\"{}\")' alt='View Details'>
                </div>";
               


                    $checked = $row['event_status'] == 1 ? 'checked' : '';
                    $status_checkbox = <<<HTML
                    <div class="toggle-switch">
                        <input type="checkbox" {$checked} id="toggle-{$row['event_id']}" class="toggle-input" onchange="change_event_status('{$row['event_id']}', this.checked)">
                        <label for="toggle-{$row['event_id']}" class="toggle-label">
                            <span class="toggle-inner"></span>
                        </label>
                    </div>
                HTML;



                    $action_buttons = "<div class='action-buttons'>
                    <img src='" . GLOBAL_PATH . "/images/svgs/eye.svg' class='action-button' 
                         onclick='view_individual_student_admission(\"{}\")' alt='View Details'>
                </div>";

                    $table_data[] = [
                        's_no' => $s_no++,
                        'event_name' => $row['event_name'],
                        'event_description' => $row['event_description'],
                        'event_start_date' => $row['event_start_date'],
                        'event_end_date' => $row['event_end_date'],
                        'event_type' => $row['event_type'],
                        'event_status' => $status_checkbox
                    ];
                }
            }
            echo json_encode([
                "draw" => intval($_POST['draw']),
                "recordsTotal" => $total_records,
                "recordsFiltered" => $filtered_records,
                "data" => $table_data,
            ]);
        } else {
            echo json_encode(['code' => $result['particulars'][0]['status_code'], 'message' => $result['particulars'][0]['message']]);
        }
    } catch (Exception $e) {
        insert_error($e->getMessage(), $_SERVER['REQUEST_URI'], 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
}
