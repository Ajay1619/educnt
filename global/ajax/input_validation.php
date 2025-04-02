<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
    $formData = isset($_POST['formData']) ? $_POST['formData'] : '';
    $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int')  : 0;
    try {
        if ($type == "1") {
            $procedure_params = [
                ['name' => 'username', 'type' => 's', 'value' => $logged_account_username],
                ['name' => 'portal type', 'type' => 'i', 'value' => 1]

            ];
            $password_validation = callProcedure("fetch_crypt", $procedure_params);
            if ($password_validation['particulars'][0]['status_code'] === 200) {
                $encryption_key = $password_validation['data'][0][0]['crypt'];
                $encrypted_password = $password_validation['particulars'][0]['account_password'];
                $account_id = $password_validation['particulars'][0]['account_id'];
                //Split the stored data to extract the encrypted part and IV
                list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
                // Re-encrypt the entered password using the same IV and key
                $re_encrypted = openssl_encrypt($old_password, 'aes-256-cbc', $encryption_key, 0, $iv);

                // Compare the re-encrypted entered password with the stored encrypted data
                $response = hash_equals($encrypted_data, $re_encrypted);
                if ($response === true) {
                    echo json_encode(['code' => 200, 'status' => 'success', 'data' => 'Password Matched']);
                } else {

                    echo json_encode(['code' => 400, 'status' => 'warning', 'data' => 'Please Enter The Correct Current Password.']);
                }
            }
        }
        if ($type == "2") {
            $password_strength = validatePassword($new_password);
            if ($password_strength === true) {
                if ($new_password == $old_password) {
                    echo json_encode(['code' => 400, 'status' => 'error', 'data' => 'New password and old password should not be same.', 'message' => 'New password and old password should not be same.']);
                    exit;
                }
                echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Password strength checked successfully.']);
                exit;
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Please Enter A Valid Password With the Constraints!', 'data' => $password_strength]);
                exit;
            }
        }
        if ($type == "3") {

            $encrypted_password = encrypt($new_password);
            $procedure_params = [
                ['name' => 'new password', 'value' => $encrypted_password, 'type' => 'i'],
                ['name' => 'logged_account_id', 'value' => $logged_account_id, 'type' => 'i'],
                ['name' => 'logged_login_id', 'value' => $logged_login_id, 'type' => 'i'],
            ];
            $update_password = callProcedure("update_password", $procedure_params);
            if ($update_password) {
                if ($update_password['particulars'][0]['status_code'] === 200) {
                    echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Password updated successfully.']);
                    exit;
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Password updation failed.']);
                    exit;
                }
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
