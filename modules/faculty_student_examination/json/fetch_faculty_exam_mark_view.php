<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && 
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token

    try {
        // Extract POST data
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';
        $exam_group_id = $_POST['exam_group_id'] ?? 0;
        $exam_type_id = $_POST['exam_type_id'] ?? 0;  
        $columns = ['exam_id', 'exam_group_id', 'exam_type_id ', 'exam_max_marks', 'exam_min_marks', 'exam_duration', 'exam_start_date', 'exam_end_date', 'exam_department_id']; 
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
            $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
            $originalData = $response['data'];
           // Function to transform the data array
function transformExamData($data) {
    $transformed = [];
    $examGroups = [];

    // Group departments by exam_id
    foreach ($data[0] as $record) {
        $examId = $record['exam_id'];
        
        // If this exam_id hasn't been processed yet
        if (!isset($examGroups[$examId])) {
            $examGroups[$examId] = [
                'exam_id' => $record['exam_id'],
                'exam_group_id' => $record['exam_group_id'],
                'exam_group_name' => $record['exam_group_name'],
                'exam_type_id' => $record['exam_type_id'],
                'exam_max_marks' => $record['exam_max_marks'],
                'exam_min_marks' => $record['exam_min_marks'],
                'exam_starting_date' => $record['exam_starting_date'],
                'exam_ending_date' => $record['exam_ending_date'],
                'exam_duration' => $record['exam_duration'],
                'exam_status' => $record['exam_status'],
                'departments' => []
            ];
        }

        // Add department to the exam's departments array
        $examGroups[$examId]['departments'][] = [
            'dept_id' => $record['dept_id'],
            'dept_title' => $record['dept_title'],
            'dept_short_name' => $record['dept_short_name'],
            'exam_dep_status' => $record['exam_dep_status']
        ];
    }

    // Convert associative array to indexed array
    $transformed = array_values($examGroups);

    return $transformed;
}

// Transform the data
$resulting = transformExamData($originalData);
// print_r($resulting);


// Print the transformed data
            $data = isset($response['data'][0]) ? $response['data'][0] : [];
            if ($response['particulars'][0]['status_code'] == 200) {
                $table_data = [];
                $s_no = $start + 1;
                

                // $action_svg_1 = GLOBAL_PATH . '/images/svgs/eye.svg';
                if (isset($response['data'][0][0]['exam_id'])) {
                    foreach ($resulting as $row) { 
                         


                        $action_svg_2 = GLOBAL_PATH . '/images/svgs/exam_mark_entry.svg';
                        $action_svg_3 = GLOBAL_PATH . '/images/svgs/datatable_delete_icon.svg';
                        $action_svg_4 = GLOBAL_PATH . '/images/svgs/examtimetable.svg';
                        $action_svg_5 = GLOBAL_PATH . '/images/svgs/tryangle.svg';
                        // Action buttons
                        // $view ="" 
                       
                    
         
                        if ($row['exam_group_id'] == 22) {
                            $exam_name = "Internal Theory"; 
                        } elseif ($row['exam_group_id'] == 23) {
                            $exam_name = "External Theory"; 
                        } elseif ($row['exam_group_id'] == 24) {
                            $exam_name = "Internal Practical"; 
                        } elseif ($row['exam_group_id'] == 25) {
                            $exam_name = "External Practical"; 
                        } 
                        // print_r();
                        $table_data[] = [
                            's_no' => $s_no++,
                            'exam_type_name' => $exam_name,
                            'exam_group_name' => $row['exam_group_name'],
                            'department_name' => $row['departments'][0]['dept_short_name'],
                            'exam_max_marks' => $row['exam_max_marks'] 
                             
                        ];
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