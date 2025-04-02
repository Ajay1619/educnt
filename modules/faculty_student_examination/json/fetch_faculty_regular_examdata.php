<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && 
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    try {
        // Extract POST data
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';
        $exam_group_id = $_POST['exam_group_id'] ?? 0;
        $exam_type_id = $_POST['exam_type_id'] ?? 0;  
        $columns = ['exam_id', 'exam_group_id', 'exam_type_id', 'exam_max_marks', 'exam_min_marks', 'exam_duration', 'exam_starting_date', 'exam_ending_date']; 
        $sort_column = ($order_column >= 0 && $order_column < count($columns)) ? $columns[$order_column] : 'exam_id';
        $department = $logged_dept_id ?? 0;

        // Prepare procedure parameters
        $procedure_params = [
            ['name' => 'p_search_value', 'value' => $search_value, 'type' => 's'],
            ['name' => 'p_sort_column', 'value' => $sort_column, 'type' => 's'],
            ['name' => 'p_order_dir', 'value' => $order_dir, 'type' => 's'],
            ['name' => 'p_start', 'value' => $start, 'type' => 'i'],
            ['name' => 'p_length', 'value' => $length, 'type' => 'i'],
            ['name' => 'p_exam_group_id', 'value' => $exam_group_id, 'type' => 'i'],
            ['name' => 'p_exam_type_id', 'value' => $exam_type_id, 'type' => 'i'], 
            ['name' => 'p_department', 'value' => $department, 'type' => 'i'],
            ['name' => 'p_login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];

        // Call the stored procedure
        $response = callProcedure('fetch_exam_management_table_data', $procedure_params); 
         
        if ($response) {
            $total_records = isset($response['data'][0][0]['total_records']) ? intval($response['data'][0][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][0][0]['filtered_records']) ? intval($response['data'][0][0]['filtered_records']) : 0;
            $exam_data = $response['data'][2]; 
            

            // Function to transform the data array
            function transformExamData($examData, $deptData) {
                $transformed = [];
                
                foreach ($examData as $exam) {
                    $examId = $exam['exam_id'];
                    // Find corresponding departments
                    $departments = array_filter($deptData, function($dept) use ($examId) {
                        return $dept['exam_id'] == $examId;
                    });

                    $transformed[] = [
                        'exam_id' => $exam['exam_id'],
                        'exam_group_id' => $exam['exam_group_id'],
                        'exam_group_name' => $exam['exam_group_name'],
                        'exam_type_id' => $exam['exam_type_id'],
                        'exam_max_marks' => $exam['exam_max_marks'],
                        'exam_min_marks' => $exam['exam_min_marks'],
                        'exam_starting_date' => $exam['exam_starting_date'],
                        'exam_ending_date' => $exam['exam_ending_date'],
                        'exam_duration' => $exam['exam_duration'],
                        'exam_status' => $exam['exam_status'],
                        'departments' => array_values($departments)
                    ];
                }

                return $transformed;
            } 
            

            if ($response['particulars'][0]['status_code'] == 200) {
                $table_data = [];
                $s_no = $start + 1;
                if (!empty($response['data'][1])) { 
                $dept_data = $response['data'][1];
                // Transform the data
                $resulting = transformExamData($exam_data, $dept_data);
                if (!empty($resulting)) {
                    
                    foreach ($resulting as $row) { 
                        $action_svg_2 = GLOBAL_PATH . '/images/svgs/exam_mark_entry.svg';
                        $action_svg_3 = GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg';
                        $action_svg_4 = GLOBAL_PATH . '/images/svgs/examtimetable.svg';
                        $action_svg_5 = GLOBAL_PATH . '/images/svgs/tryangle.svg';

                        $action_buttons = '<div class="action-buttons">';
                        
                        if ($logged_role_id != 7) {
                            $action_buttons .= <<<HTML
                                <img src="{$action_svg_4}" class="action-button" id="view_exam" data-exam_id="{$row['exam_id']}" data-exam_group_id="{$row['exam_group_id']}" data-exam_type_id="{$row['exam_type_id']}" data-exam_duration="{$row['exam_duration']}" data-exam_start_date="{$row['exam_starting_date']}" data-exam_end_date="{$row['exam_ending_date']}" onclick="view_exam(this)">
                                <!-- <img src="{$action_svg_5}" class="action-button" id="view_exam" data-exam_id="{$row['exam_id']}" data-exam_group_id="{$row['exam_group_id']}" data-exam_type_id="{$row['exam_type_id']}" data-exam_duration="{$row['exam_duration']}" data-exam_start_date="{$row['exam_starting_date']}" data-exam_end_date="{$row['exam_ending_date']}" onclick="view_exam_result(this)"> -->
                            HTML;
                        }
                        
                        $action_buttons .= <<<HTML
                            <img src="{$action_svg_2}" class="action-button" data-exam_id="{$row['exam_id']}" data-exam_group_id="{$row['exam_group_id']}" data-exam_type_id="{$row['exam_type_id']}" data-exam_duration="{$row['exam_duration']}" data-exam_start_date="{$row['exam_starting_date']}" data-exam_end_date="{$row['exam_ending_date']}" onclick="exam_mark_entry_layout(this)">
                            <!-- <img src="{$action_svg_3}" class="action-button" data-exam_id="{$row['exam_id']}" data-exam_group_id="{$row['exam_group_id']}" data-exam_type_id="{$row['exam_type_id']}" data-exam_duration="{$row['exam_duration']}" data-exam_start_date="{$row['exam_starting_date']}" data-exam_end_date="{$row['exam_ending_date']}" onclick="view_exam(this)"> -->
                        </div>
                        HTML;

                        $exam_name = "";
                        switch ($row['exam_group_id']) {
                            case 22:
                                $exam_name = "Internal Theory";
                                break;
                            case 23:
                                $exam_name = "External Theory";
                                break;
                            case 24:
                                $exam_name = "Internal Practical";
                                break;
                            case 25:
                                $exam_name = "External Practical";
                                break;
                        }

                        $table_data[] = [
                            's_no' => $s_no++,
                            'exam_type_name' => $exam_name,
                            'exam_group_name' => $row['exam_group_name'],
                            'department_name' => $row['departments'][0]['dept_short_name'] ?? 'N/A',
                            'exam_max_marks' => $row['exam_max_marks'],
                            'exam_min_marks' => $row['exam_min_marks'],
                            'exam_duration' => $row['exam_duration'] . "hrs",
                            'action' => $action_buttons
                        ];

                    }
                }
            }
                

                $result = [
                    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                    "recordsTotal" => $total_records,
                    "recordsFiltered" => $filtered_records,
                    "data" => $table_data
                ];
                echo json_encode($result);
                exit;
            } else {
                echo json_encode([
                    'code' => $response['particulars'][0]['status_code'],
                    'status' => $response['particulars'][0]['status'],
                    'message' => $response['particulars'][0]['message']
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
?>