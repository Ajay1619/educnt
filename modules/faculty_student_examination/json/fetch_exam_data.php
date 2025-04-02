<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try { 
        // [data] => Array
        // (
        //     [exam_id] => 72
        //     [exam_group_id] => 22
        //     [exam_type_id] => 327
        //     [exam_duration] => 2.00
        //     [exam_start_date] => 2025-03-08
        //     [exam_end_date] => 2025-03-15
        // )
        $exam_id = isset($_POST['data']['exam_id']) ? sanitizeInput($_POST['data']['exam_id'], 'int') : 0;
        $exam_group_id = isset($_POST['data']['exam_group_id']) ? sanitizeInput($_POST['data']['exam_group_id'], 'int') : 0;
        $exam_type_id = isset($_POST['data']['exam_type_id']) ? sanitizeInput($_POST['data']['exam_type_id'], 'int') : 0;
        
        
         




        $params_procedures = [
             ['name' => 'login_id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id], 
            ['name' => 'exam_id', 'type' => 'i', 'value' => $exam_id], 
            ['name' => 'exam_group_id', 'type' => 'i', 'value' => $exam_group_id], 
            ['name' => 'exam_type_id', 'type' => 'i', 'value' => $exam_type_id] ];
        
        $result = callProcedure("fetch_pr_exam_data", $params_procedures);
        // print_r($result);
        // exit;
        
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if ($result['data']) {
                    $year_of_study_data = $result['data']; 
                    $originalData = $result['data'];
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
                    $resulting = transformExamData($originalData);
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $resulting , 'message' => $result['particulars'][0]['message']]);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No Year Of Study Found For Selected Department.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found.']);
            exit;
        }
    } catch (\Throwable $th) {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
