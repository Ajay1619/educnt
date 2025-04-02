<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $params_procedures=[
            ['name'=>'login_id',
            'type'=>'i',
            'value'=>$logged_login_id],
        ];
        $result = callProcedure("fetch_pr_community",$params_procedures);
      
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                $community_data = [];
                foreach ($result['data'][0] as $key => $value) {
                    $community_data[] = ['value' => $value['id'], 'title' => $value['title']];
                }
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'data' => $community_data, 'message' => $result['particulars'][0]['message']]);
                exit;
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
