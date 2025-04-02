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
    try {
        $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];

        $room_id = isset($_POST['room_id']) ? sanitizeInput(decrypt_data($_POST['room_id']), 'int') : 0;

        $procedure_params = [
            ['name' => 'room_id', 'value' => $room_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_individual_dept_room_details", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0][0])) {
                    $data = $result['data'][0][0];


                    $edit_form = "
                        <input type='hidden' id='room-id' name='room_id' value='{$room_id}' required>
                        <div class='row'>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='room-number' name='room_number' placeholder=' ' value='{$data['room_number']}' required>
                                    <label class='input-label' for='room-number'>Enter The Room Number</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='room-name' name='room_name' placeholder=' ' value='{$data['room_name']}' required>
                                    <label class='input-label' for='room-name'>Enter The Room Name</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container dropdown-container'>
                                    <input type='text' id='room-floor-dummy' name='selected-subjects' class='auto dropdown-input' placeholder=' ' value='{$data['floor_title']}' readonly>
                                    <label class='input-label' for='room-floor-dummy'>Select Floor</label>
                                    <input type='hidden' name='room_floor' class='room-floor-filter' id='room-floor' value='{$data['room_floor']}' required>
                                    <span class='dropdown-arrow'>&#8964;</span>
                                    <div class='dropdown-suggestions'></div>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='room-category' name='room_category'  value='{$data['room_category']}' placeholder=' ' required>
                                    <label class='input-label' for='room-category'>Enter The Room Category</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='room-max-capacity' name='room_max_capacity'  value='{$data['max_capacity']}'  placeholder=' ' required>
                                    <label class='input-label' for='room-max-capacity'>Enter The Room Max Capacity</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
            
                                <div class='input-group' id='room-type'>
                                    <p for='room-type'>Select The Room Type</p>
            
                                    <label class='modern-radio'>
                                        <input type='radio' name='room_type' value='1' " . ($data['room_type'] == 1 ? "checked" : "") . ">
                                        <span></span>
                                        <div class='modern-label'>Teaching Use</div>
                                    </label>
                                    
                                    <label class='modern-radio'>
                                        <input type='radio' name='room_type' value='2' " . ($data['room_type'] == 2 ? "checked" : "") . ">
                                        <span></span>
                                        <div class='modern-label'>Office Use</div>
                                    </label>
                                </div>
            
                            </div>
                        </div>
                        <button type='submit' class='primary text-center full-width mt-6'>SUBMIT</button>
                    ";

                    $view_data = "
                    <input type='hidden' id='room-id' name='room_id' value='{$room_id}' required>
                    <div class='row'>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-left'>Room Number :
                                <span class='text-light' id='room-number'>{$data['room_number']} </span>
                            </div>
                        </div>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-right'>Room Name :
                                <span class='text-light' id='room-name'>{$data['room_name']}</span>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-left'>Floor :
                                <span class='text-light' id='floor'>{$data['floor_title']}</span>
                            </div>
                        </div>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-right'>Room Category :
                                <span class='text-light' id='room-category'>{$data['room_category']}</span>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-left'>Max Capacity :
                                <span class='text-light' id='max-capacity'>{$data['max_capacity']}</span>
                            </div>
                        </div>
                        <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                            <div class='section-header-title text-right'>Room Type :
                                <span class='text-light' id='room-type'>{$data['room_type_title']}</span>
                            </div>
                        </div>
                    </div>
                    ";
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'edit_form' => $edit_form, 'view_data' => $view_data]);
                    exit;
                } else {
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No Room data found with your Filter.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
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
