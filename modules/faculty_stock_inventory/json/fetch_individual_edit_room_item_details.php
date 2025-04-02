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

        $item_id = isset($_POST['item_id']) ? sanitizeInput(decrypt_data($_POST['item_id']), 'int') : 0;

        $procedure_params = [
            ['name' => 'item_id', 'value' => $item_id, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_individual_dept_room_item_details", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0][0])) {
                    $data = $result['data'][0][0];

                    $edit_form = "
                    <!-- Popup Content -->
                    <div class='popup-content'>
                    <input type='hidden' id='item-id' name='item_id' value='{$item_id}' required>
                        <div class='row'>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='item-name' name='item_name' value='{$data['item_name']}' placeholder=' ' required>
                                    <label class='input-label' for='item-name'>Enter The Item Name</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='unit-of-measure' name='unit_of_measure' placeholder=' '  value='{$data['item_unit_of_measure']}' required>
                                    <label class='input-label' for='unit-of-measure'>Enter The Unit Of Measure</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='item-quantity' name='item_quantity' placeholder=' ' value='{$data['item_quantity']}' required>
                                    <label class='input-label' for='item-quantity'>Enter The Quantity Of Item</label>
                                </div>
                            </div>
                            <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                                <div class='input-container '>
                                    <input type='text' id='item-note' name='item_note' placeholder=' ' value='{$data['item_description']}' required>
                                    <label class='input-label' for='item-note'>Enter Any Note For The Item</label>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Popup Footer -->
                    <div class='popup-footer'>
                        <button type='submit' class='btn-success'>Submit</button>
                        <button type='button' class='btn-error deny-button'>Cancel</button>
                    </div>
                    ";

                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'edit_form' => $edit_form]);
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
