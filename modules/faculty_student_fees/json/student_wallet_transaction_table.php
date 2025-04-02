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

        if (in_array($logged_role_id, $primary_roles) && in_array($logged_role_id, $higher_official)) {
            $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
        } else {
            $dept_id = $logged_dept_id;
        }

        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int') : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
        $student_wallet_id = isset($_POST['student_wallet_id']) && $_POST['student_wallet_id'] != 0 ? sanitizeInput(decrypt_data($_POST['student_wallet_id']), 'int') : 0;
        $columns = ['student_name', 'register_number', 'amount', 'wallet_action', 'wallet_status'];

        if ($order_column >= 0 && $order_column < count($columns)) {
            $sort_column = $columns[$order_column];
        } else {
            $sort_column = 'register_number'; // Default sort column
        }

        // Prepare input parameters for stored procedure or SQL query
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $dept_id],
            ['type' => 'i', 'value' => $year_of_study_id],
            ['type' => 'i', 'value' => $section_id],
            ['type' => 'i', 'value' => $logged_login_id],
            ['type' => 'i', 'value' => $student_wallet_id],
        ];

        $response = callProcedure('fetch_pr_wallet_transaction_table', $inputParams);

        if ($response['particulars'][0]['status_code'] == 200) {
            $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
            $data = isset($response['data'][0]) ? $response['data'][0] : [];
            if ($type == 1) {
                $table_data = [];
                $s_no = $start + 1;

                if (isset($response['data'][0][0]['student_wallet_id'])) {
                    foreach ($data as $row) {
                        $profile_pic = !empty($row['profile_pic_path']) ? GLOBAL_PATH . '/uploads/student_profile_pic/' . $row['profile_pic_path'] : GLOBAL_PATH . '/images/profile pic placeholder.png'; // Default image if no profile 
                        $student_wallet_id = encrypt_data($row['student_wallet_id']);
                        $student_name = $row['student_name'];
                        $register_number = $row['register_number'];
                        $amount = $row['amount'];
                        $wallet_action = $row['wallet_action'];
                        $wallet_status = $row['wallet_status'];

                        $wallet_action_badge = $wallet_action == 1 ? '<span class="alert alert-info">Credit</span>' : '<span class="alert alert-error">Debit</span>';

                        $wallet_status_badge = $wallet_status == 0 ? '<span class="alert alert-warning">Pending</span>' : '<span class="alert alert-success">Confirmed</span>';

                        $student_first_name = <<<HTML
                        <div class="student-info">
                            <div class="row align-items-center" >
                                <img src="{$profile_pic}" alt="student Avatar" class="student-avatar-img"> 
                                <span class="student-name ml-4">{$student_name}</span>
                            </div> 
                        </div>
                        HTML;
                        // Status switch checkbox


                        $action_svg_1 = GLOBAL_PATH . '/images/svgs/confirm_success_icon.svg';
                        $action_svg_2 = GLOBAL_PATH . '/images/svgs/confirm_error_icon.svg';
                        $action_svg_3 = GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg';
                        $action_svg_4 = GLOBAL_PATH . '/images/svgs/eye.svg';
                        // Action buttons
                        $action_buttons = <<<HTML
                        <div class="action-buttons">
                        HTML;

                        // Add conditional buttons based on wallet_status
                        if ($wallet_status != 1 && $wallet_status != 3) {
                            $action_buttons .= <<<HTML
                                <img src="{$action_svg_1}" class="action-button" onclick="load_wallet_transaction_confirm('{$student_wallet_id}','{$wallet_action}',1)">
                                <img src="{$action_svg_2}" class="action-button" onclick="load_wallet_transaction_confirm('{$student_wallet_id}','{$wallet_action}',3)">
                                <img src="{$action_svg_3}" class="action-button" onclick="load_edit_wallet_transaction('{$student_wallet_id}',{$wallet_action})">
                        HTML;
                        }

                        // Always show the view button
                        $action_buttons .= <<<HTML
                            <img src="{$action_svg_4}" class="action-button" onclick="load_view_wallet_transaction('{$student_wallet_id}')">
                        </div>
                        HTML;

                        // Add the row data to the table
                        $table_data[] = [
                            's_no' => $s_no++,
                            'student_name' => $student_first_name,
                            'register_number' => $register_number,
                            'amount' => $amount,
                            'wallet_action' => $wallet_action_badge,
                            'wallet_status' => $wallet_status_badge,
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
            } elseif ($type == 2) {
                $data = isset($response['data'][0][0]) ? $response['data'][0][0] : [];
                $wallet_action = $data['wallet_action'];
                $wallet_status = $data['wallet_status'];

                $wallet_action_badge = $wallet_action == 1 ? '<span class="alert alert-info">Credit</span>' : '<span class="alert alert-error">Debit</span>';

                $wallet_status_badge = $wallet_status == 0 ? '<span class="alert alert-warning">Pending</span>' : '<span class="alert alert-success">Confirmed</span>';
                $data['wallet_action_badge'] = $wallet_action_badge;
                $data['wallet_status_badge'] = $wallet_status_badge;
                echo json_encode(['code' => $response['particulars'][0]['status_code'], 'status' => $response['particulars'][0]['status'], 'message' => $response['particulars'][0]['message'], 'data' => $data]);
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
