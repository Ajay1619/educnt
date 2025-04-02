<?php
//checklogin function
function checkLogin($login_id)
{
    $check_login_procedure_params = [
        ['value' => $login_id, 'type' => 'i']
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('check_user_login_status', $check_login_procedure_params);

        // Handle the result
        if ($result['particulars'][0]['status_code'] !== 200) {
            // User login status is valid
            session_destroy();
            echo "<script>window.location.href='" . BASEPATH . "';</script>";
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function insert_error($error_message = "Error!!", $location_href = '', $error_side = 0)
{
    $check_login_procedure_params = [
        ['value' => $_SESSION['svcet_educnt_faculty_login_id'], 'type' => 'i'],
        ['value' => $error_side, 'type' => 'i'],
        ['value' => $location_href, 'type' => 'i'],
        ['value' => $error_message, 'type' => 'i'],
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('insert_error_log', $check_login_procedure_params);
        return $result;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}



// checkPageAccess

function checkPageAccess($faculty_page_access_data, $page_link)
{
    $page_id = 0;

    // Parse the current URL to separate the base path and query string
    $parsed_url = parse_url($page_link);
    $current_path = $parsed_url['path'];
    $current_query = isset($parsed_url['query']) ? $parsed_url['query'] : '';

    // Convert the current query string into an associative array
    parse_str($current_query, $current_query_params);

    // If 'id' exists in the query parameters, replace its value with a wildcard
    if (isset($current_query_params['id'])) {
        $current_query_params['id'] = '*';
    }

    // Rebuild the modified query string
    $current_query = http_build_query($current_query_params);
    $normalized_current_link = $current_path . ($current_query ? '?' . $current_query : '');

    // Iterate through the page access data
    foreach ($faculty_page_access_data as $page) {

        // Prepend the base path to the page link
        $page['page_link'] = "/educnt-svcet-faculty/" . $page['page_link'];
        $page['page_link'] = trim($page['page_link']);
        // Parse the page link to separate path and query string
        $allowed_url = parse_url($page['page_link']);
        $allowed_path = $allowed_url['path'];
        $allowed_query = isset($allowed_url['query']) ? $allowed_url['query'] : '';

        // Convert the allowed query string into an associative array
        parse_str($allowed_query, $allowed_query_params);

        // If 'id' exists in the allowed query parameters, replace its value with a wildcard
        if (isset($allowed_query_params['id'])) {
            $allowed_query_params['id'] = '*';
        }

        // Rebuild the modified query string for the allowed page
        $allowed_query = http_build_query($allowed_query_params);
        $normalized_allowed_link = $allowed_path . ($allowed_query ? '?' . $allowed_query : '');
        // Compare the normalized links
        if ($normalized_allowed_link === $normalized_current_link) {
            $page_id = $page['page_id'];
            break;
        }
    }

    // Handle access denial if no match is found
    if ($page_id === 0) {
        // Uncomment these lines to enforce access control
        // session_destroy();
        // echo "<script>window.location.href='" . BASEPATH . "/user-access-denied';</script>";
    }
}

// Function to generate and regenerate a CSRF token for every request
function generateCsrfToken()
{

    // Generate a random string as the CSRF token (32 bytes of randomness)
    $token = bin2hex(random_bytes(32));

    // Store the newly generated token in the session
    return $token;
}

// Function to validate the CSRF token
function validateCsrfToken($token)
{

    // Check if the session CSRF token matches the provided token
    if (isset($_SESSION['sparrow_faculty_csrf_token']) && hash_equals($_SESSION['sparrow_faculty_csrf_token'], $token)) {
        // Regenerate a new CSRF token for the next request
        return true;
    } else {
        // Invalid CSRF token
        session_destroy();
        echo "<script>window.location.href='" . BASEPATH . "/unauthorized-access';</script>";
    }
}


function getUserIP()
{
    $ip = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipList[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


// Function to get the current location
function getCurrentLocation(): string // Specify return type (optional)
{
    // Extract the location (file name) from the URL
    $location = explode('/', $_SERVER['PHP_SELF']);
    $location = end($location); // Get the last element (file name)

    // Remove the ".php" extension
    $location = pathinfo($location, PATHINFO_FILENAME);

    // Check if the location is "index" and treat it as "Login"
    if ($location === "index") {
        $location = "Login";
    }


    // Retrieve the "type" parameter from the URL if it exists
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $location = str_replace("_", " ", capitalizeFirstLetter($location)); // Capitalize the first letter of each word
    $type = str_replace("_", " ", capitalizeFirstLetter($type)); // Capitalize the first letter of each word
    $action = str_replace("_", " ", capitalizeFirstLetter($action)); // Capitalize the first letter of each word
    // Concatenate "Sparrow | " with the type and current location
    if (!empty($type)) {
        return "$type $location $action | EDUCNT";
    } else {
        return "$location | EDUCNT";
    }
}

// Function to get the current URL path
function getRewrittenUrl(): string
{
    // Retrieve the URL path from the request URI
    $uri = $_SERVER['REQUEST_URI'];

    // Remove query string if present
    $uri = strtok($uri, '?');

    // Trim any leading/trailing slashes and explode by '/'
    $parts = explode('/', trim($uri, '/'));

    // Get the last segment 
    $lastSegment = end($parts);
    $lastSegment = trim($lastSegment);
    $lastSegment = str_replace('_', ' ', $lastSegment);
    // Remove the ".php" extension
    $lastSegment = pathinfo($lastSegment, PATHINFO_FILENAME);
    return capitalizeFirstLetter($lastSegment);
}


function uploadFile($fileInput, $destinationDir, $prefix = '')
{
    // Array to hold the status and errors
    $result = ['status_code' => 200, 'status' => 'success', 'files' => [], 'message' => ''];

    // Ensure the destination directory exists
    if (!file_exists($destinationDir)) {
        if (!mkdir($destinationDir, 0777, true)) {
            return ['status_code' => 400, 'status' => 'error', 'message' => 'Failed to create destination directory.'];
        }
    }

    // Ensure the temporary directory exists
    $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('upload_', true);
    if (!mkdir($tempDir)) {
        return ['status_code' => 400, 'status' => 'error', 'message' => 'Failed to create temporary directory.'];
    }

    // Check if the input is an array of files or a single file
    $isMultiple = is_array($fileInput['name']);

    // Process files
    $filesCount = $isMultiple ? count($fileInput['name']) : 1;
    for ($i = 0; $i < $filesCount; $i++) {
        $fileName = $isMultiple ? $fileInput['name'][$i] : $fileInput['name'];
        $fileTmpName = $isMultiple ? $fileInput['tmp_name'][$i] : $fileInput['tmp_name'];
        $fileError = $isMultiple ? $fileInput['error'][$i] : $fileInput['error'];

        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            $result['status'] = 'error';
            $result['message'] = "Error uploading file: $fileName. Error code: $fileError.";
            continue;
        }

        // Move the uploaded file to the temporary directory
        $tempFilePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($fileTmpName, $tempFilePath)) {
            $result['status'] = 'error';
            $result['message'] = "Failed to move file to temp directory: $fileName.";
            continue;
        }

        // Generate new file name with format: prefix-filename-currenttimestamp.extension
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME); // Get the original filename without extension
        $timestamp = date(FILE_DATETIME_FORMAT); // Get the current timestamp
        $newFileName = $prefix . $baseName . '-' . $timestamp . '.' . $fileExt;
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $newFileName;

        // Check if the file already exists in the destination directory
        if (file_exists($destinationPath)) {
            // If the file exists, delete it
            if (!unlink($destinationPath)) {
                $result['status'] = 'error';
                $result['message'] = "Failed to delete existing file: $newFileName.";
                continue;
            }
        }

        // Move the file to the destination directory
        if (rename($tempFilePath, $destinationPath)) {
            $result['files'][] = $newFileName;
        } else {
            $result['status'] = 'error';
            $result['message'] = "Failed to move file to destination directory: $fileName.";
        }
    }

    // Clean up temporary directory
    foreach (glob($tempDir . '/*') as $file) {
        unlink($file);
    }
    rmdir($tempDir);

    return $result;
}



//check empty fields
function checkEmptyField($field, $fieldName)
{
    if ($field == null || $field == "" || $field == " ") {
        return "Error: $fieldName is required.";
    }
    return "";
}

//1. Validate Length with value and length
function validateLength($value, $length)
{
    if (strlen($value) <= $length) {
        return false;
    } else {
        return true;
    }
}

//2.validate Email
function isEmail($email)
{
    return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? true : false;
}

//isYear
function isYear($year)
{
    return preg_match('/^\d{4}$/', $year) ? true : false;
}
//isPercentage 
function isPercentage($percentage)
{
    return preg_match('/^100(\.0{1,2})?$|^\d{1,2}(\.\d{1,2})?$/', $percentage) ? true : false;
}

function base62_encode($data)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($characters);
    $encoded = '';

    $data = unpack('H*', $data)[1];
    $data = gmp_init($data, 16);

    while (gmp_cmp($data, 0) > 0) {
        $remainder = gmp_mod($data, $base);
        $encoded = $characters[gmp_intval($remainder)] . $encoded;
        $data = gmp_div_q($data, $base);
    }

    return $encoded;
}

function base62_decode($data)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($characters);
    $decoded = gmp_init(0);

    for ($i = 0, $len = strlen($data); $i < $len; $i++) {
        $decoded = gmp_add(gmp_mul($decoded, $base), strpos($characters, $data[$i]));
    }

    $decoded = gmp_strval($decoded, 16);
    return pack('H*', str_pad($decoded, ceil(strlen($decoded) / 2) * 2, '0', STR_PAD_LEFT));
}

function encrypt_data($data)
{
    // Fetch the encryption key dynamically
    $fetch_crypt_procedure_params = [
        ['value' => "", 'type' => 's'],
        ['value' => 1, 'type' => 'i']
    ];
    $result = callProcedure("fetch_crypt", $fetch_crypt_procedure_params);
    $encryption_key = $result['data'][0][0]['crypt'];

    // Generate a random IV
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-ctr'));

    // Encrypt the data using AES-128-CTR
    $encrypted = openssl_encrypt($data, 'aes-128-ctr', $encryption_key, 0, $iv);

    // Concatenate IV and encrypted data
    $combined = $iv . $encrypted;

    // Return Base62 encoded result
    return base62_encode($combined);
}

function decrypt_data($encryptedData)
{
    // Fetch the encryption key dynamically
    $fetch_crypt_procedure_params = [
        ['value' => "", 'type' => 's'],
        ['value' => 1, 'type' => 'i']
    ];
    $result = callProcedure("fetch_crypt", $fetch_crypt_procedure_params);
    $encryption_key = $result['data'][0][0]['crypt'];

    // Decode Base62 encoded string
    $combined = base62_decode($encryptedData);

    // Extract IV and encrypted text
    $iv_length = openssl_cipher_iv_length('aes-128-ctr');
    $iv = substr($combined, 0, $iv_length);
    $encrypted = substr($combined, $iv_length);

    // Decrypt the data
    return openssl_decrypt($encrypted, 'aes-128-ctr', $encryption_key, 0, $iv);
}



function encrypt($data)
{
    $fetch_crypt_procedure_params = [
        ['value' => "", 'type' => 's'],
        ['value' => 1, 'type' => 'i']
    ];
    $result = callProcedure("fetch_crypt", $fetch_crypt_procedure_params);
    $encryption_key = $result['data'][0][0]['crypt'];
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decrypt($encryptedData)
{
    // Fetch the encryption key, just like in the encryption function
    $fetch_crypt_procedure_params = [
        ['value' => "", 'type' => 's'],
        ['value' => 1, 'type' => 'i']
    ];
    $result = callProcedure("fetch_crypt", $fetch_crypt_procedure_params);

    $encryption_key = $result['data'][0][0]['crypt'];
    // Split the encrypted data and IV
    list($encrypted, $iv) = explode('::', base64_decode($encryptedData), 2);

    // Decrypt the data
    return openssl_decrypt($encrypted, 'aes-256-cbc', $encryption_key, 0, $iv);
}
function passwordVerify($entered_password, $user_name, $logged_portal_type)
{
    $fetch_crypt_procedure_params = [
        ['value' => $user_name, 'type' => 's'],
        ['value' => $logged_portal_type, 'type' => 'i']
    ];
    $result = callProcedure("fetch_crypt", $fetch_crypt_procedure_params);

    if ($result) {
        if ($result['particulars'][0]['status_code'] === 400) {
            return $result['particulars'][0];
        } else {
            $encryption_key = $result['data'][0][0]['crypt'];
            $encrypted_password = $result['particulars'][0]['account_password'];
            $account_id = $result['particulars'][0]['account_id'];
            //Split the stored data to extract the encrypted part and IV
            list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
            // Re-encrypt the entered password using the same IV and key
            $re_encrypted = openssl_encrypt($entered_password, 'aes-256-cbc', $encryption_key, 0, $iv);

            // Compare the re-encrypted entered password with the stored encrypted data
            $response = hash_equals($encrypted_data, $re_encrypted);
            if ($response === true) {
                $login_validate_procedure_params = [
                    ['name' => 'user id', 'value' => $account_id, 'type' => 'i'],
                    ['name' => 'portal type', 'value' => $logged_portal_type, 'type' => 'i'],
                    ['name' => 'log id', 'value' => 0, 'type' => 'i'],
                    ['name' => 'user IP address', 'value' => getUserIP(), 'type' => 's'],
                    ['name' => 'successful login', 'value' => 1, 'type' => 'i'],
                    ['name' => 'login status', 'value' => 1, 'type' => 'i'],
                    ['name' => 'log out', 'value' => 1, 'type' => 'i']
                ];
                $login_validation_result = callProcedure("login_validate", $login_validate_procedure_params);

                if ($login_validation_result) {
                    if ($login_validation_result['particulars'][0]['status_code'] === 200) {

                        $logged_fetched_user_data = $login_validation_result['data'][0][0];
                        if (!isset($login_validation_result['data'][1])) {
                            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'You Have No Permission!']);
                            exit;
                        }
                        $logged_fetched_user_page_access_data = $login_validation_result['data'][1];
                        $login_validation_result = [
                            "status_code" => $login_validation_result['particulars'][0]['status_code'],
                            'status' => $login_validation_result['particulars'][0]['status'],
                            'message' => $login_validation_result['particulars'][0]['message'],
                            'user_data' => $logged_fetched_user_data,
                            'user_page_access_data' => $logged_fetched_user_page_access_data

                        ];
                        return $login_validation_result;
                    } else {
                        $login_validation_result = [
                            "status_code" => $login_validation_result['particulars'][0]['status_code'],
                            'status' => $login_validation_result['particulars'][0]['status'],
                            'message' => $login_validation_result['particulars'][0]['message'],
                        ];
                        return $login_validation_result;
                    }
                }
            } else {
                $login_validate_procedure_params = [
                    ['name' => 'user id', 'value' => $account_id, 'type' => 'i'],
                    ['name' => 'portal type', 'value' => $logged_portal_type, 'type' => 'i'],
                    ['name' => 'log id', 'value' => 0, 'type' => 'i'],
                    ['name' => 'user IP address', 'value' => getUserIP(), 'type' => 's'],
                    ['name' => 'successful login', 'value' => 0, 'type' => 'i'],
                    ['name' => 'login status', 'value' => 3, 'type' => 'i'],
                    ['name' => 'log out', 'value' => 1, 'type' => 'i']
                ];
                $login_validation_result = callProcedure("login_validate", $login_validate_procedure_params);

                $login_validation_result = [
                    "status_code" => $login_validation_result['particulars'][0]['status_code'],
                    'status' => $login_validation_result['particulars'][0]['status'],
                    'message' => $login_validation_result['particulars'][0]['message'],
                ];
                return $login_validation_result;
            }
        }
    }
}

function user_logout()
{
    $logout_validate_procedure_params = [
        ['name' => 'user id', 'value' => $_SESSION['svcet_educnt_account_id'], 'type' => 'i'],
        ['name' => 'portal type', 'value' => $_SESSION['svcet_educnt_logged_portal_type'], 'type' => 'i'],
        ['name' => 'log id', 'value' => $_SESSION['svcet_educnt_login_id'], 'type' => 'i'],
        ['name' => 'user IP address', 'value' => getUserIP(), 'type' => 's'],
        ['name' => 'successful login', 'value' => 0, 'type' => 'i'],
        ['name' => 'login status', 'value' => 1, 'type' => 'i'],
        ['name' => 'log out', 'value' => 0, 'type' => 'i']
    ];
    $logout_validation_result = callProcedure("login_validate", $logout_validate_procedure_params);
    if ($logout_validation_result) {
        if ($logout_validation_result['particulars'][0] === 200) {
            session_destroy();
            echo "<script>window.location.href='" . BASEPATH . "';</script>";
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to logout']);
        }
    }
}

function validatePassword($password)
{
    $errors = [];

    // Check if the password has at least 8 characters
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check if the password contains at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }

    // Check if the password contains at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check if the password contains at least one symbol
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must contain at least one symbol (e.g., !@#$%^&*).";
    }

    // Return errors if any, otherwise return true
    return empty($errors) ? true : $errors;
}



//5. Validate Phone Number 
function isPhoneNumber($phone)
{
    // Check if the phone number starts with 6-9 and is exactly 10 digits long
    return preg_match('/^[6-9]\d{9}$/', $phone) ? true : false;
}


// 6. Is Not Null
function isNotNull($value)
{
    if ($value == null) {
        return false;
    } else {
        return true;
    }
}

// 7. sanitize input
function sanitizeInput(mixed $value, string $type = 'string'): mixed
{
    if (is_array($value)) {
        return array_map(fn($item) => sanitizeInput($item, $type), $value);
    }

    // Attempt to convert the value to the specified type
    switch ($type) {
        case 'int':
            $value = (int)$value;
            break;
        case 'float':
            $value = (float)$value;
            break;
        case 'bool':
        case 'boolean':
            // Handle boolean casting with explicit checks for known true/false values
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value === null) {
                $value = false; // Default to false if not a valid boolean representation
            }
            break;
        case 'email':
        case 'url':
        case 'string':
            $value = (string)$value;
            break;
        default:
            throw new InvalidArgumentException("Invalid type specified: $type");
    }

    // Trim leading/trailing whitespace if it's a string type
    if (is_string($value)) {
        $value = trim($value);
    }

    // Sanitize based on input type
    switch ($type) {
        case 'string':
            $value = htmlspecialchars($value, ENT_QUOTES);
            break;
        case 'int':
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            break;
        case 'float':
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            break;
        case 'email':
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
            break;
        case 'url':
            $value = filter_var($value, FILTER_SANITIZE_URL);
            break;
        case 'bool':
        case 'boolean':
            // No additional sanitization needed for boolean after validation above
            break;
    }

    return $value;
}



function formatAndValidateAadharNumber($input)
{
    // Remove any existing whitespace for initial validation
    $input = str_replace(' ', '', $input);

    // Check if input starts with 0 or 1 and contains only digits
    if (!preg_match('/^[2-9]\d*$/', $input)) {
        return [
            'message' => 'Invalid input: Must start with 2-9 and contain only digits.',
            'status_code' => 300,
            'status' => 'warning'
        ];
    }

    // Format the number to have whitespace after every 4 digits
    $formattedInput = preg_replace('/(\d{4})(?=\d)/', '$1 ', $input);

    return [
        'formatted_number' => $formattedInput,
        'status_code' => 200,
        'status' => 'success'
    ];
}

// 8.capitalize first letter after all whitespaces
function capitalizeFirstLetter($value)
{
    return ucwords(strtolower($value));
}

//9.remove whitespaces at start and end
function removeWhitespaces($value)
{
    return trim($value);
}

//10. get current date 
function getCurrentDate()
{
    return date(DATE_FORMAT);
}

//11. get current time in 12 hrs format
function getCurrentTime()
{
    return date(TIME_FORMAT);
}

//12. get current date and time
function getCurrentDateTime()
{
    return date(DATETIME_FORMAT);
}

//13.Show Time stamp.  Eg.: few seconds ago
function showTimeStamp(int|string $timeStamp): string
{
    $timeStamp = strtotime($timeStamp);
    $timeElapsed = time() - $timeStamp;  // Time elapsed since the timestamp

    $timeUnits = [
        // Units in descending order of magnitude
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    ];

    foreach ($timeUnits as $unit => $secondsInUnit) {
        if ($timeElapsed >= $secondsInUnit) {
            $numberOfUnits = floor($timeElapsed / $secondsInUnit);
            return "$numberOfUnits {$unit}" . ($numberOfUnits > 1 ? 's' : '') . ' ago';
        }
    }

    // If no matching unit found, fall back to seconds
    return "a few seconds ago";
}

function convertNumberToWords($number)
{
    $dictionary = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        100000 => 'lakh',
        10000000 => 'crore'
    ];

    if ($number < 0) {
        return 'minus ' . convertNumberToWords(-$number);
    }

    if ($number === 0) {
        return $dictionary[0];
    }

    // Split the number into integer and decimal parts
    $numberParts = explode('.', number_format($number, 2, '.', ''));
    $integerPart = (int) $numberParts[0];
    $fractionPart = isset($numberParts[1]) ? (int) $numberParts[1] : 0;

    $words = [];

    // Convert integer part to words
    $words[] = convertIntegerToWords($integerPart, $dictionary);

    // Add "and" for decimal part
    if ($fractionPart > 0) {
        $words[] = 'and';
        $words[] = convertIntegerToWords($fractionPart, $dictionary) . ' paise';
    }

    return implode(' ', $words);
}

function convertIntegerToWords($number, $dictionary)
{
    if ($number === 0) {
        return $dictionary[0];
    }

    $words = [];

    // Handle crores
    if ($number >= 10000000) {
        $crore = intdiv($number, 10000000);
        $words[] = convertIntegerToWords($crore, $dictionary) . ' crore';
        $number %= 10000000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle lakhs
    if ($number >= 100000) {
        $lakh = intdiv($number, 100000);
        $words[] = convertIntegerToWords($lakh, $dictionary) . ' lakh';
        $number %= 100000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle thousands
    if ($number >= 1000) {
        $thousand = intdiv($number, 1000);
        $words[] = convertIntegerToWords($thousand, $dictionary) . ' thousand';
        $number %= 1000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle hundreds
    if ($number >= 100) {
        $hundred = intdiv($number, 100);
        $words[] = convertIntegerToWords($hundred, $dictionary) . ' hundred';
        $number %= 100;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle tens and units
    if ($number > 0) {
        if ($number < 20) {
            $words[] = $dictionary[$number];
        } else {
            $tens = intdiv($number, 10) * 10;
            $units = $number % 10;
            $words[] = $dictionary[$tens];
            if ($units > 0) {
                $words[] = $dictionary[$units];
            }
        }
    }

    return implode(' ', $words);
}




// Function to format numbers in Indian style
function formatNumberIndian($number)
{
    // Convert number to string with two decimal places
    $number = number_format($number, 2, '.', '');
    $parts = explode('.', $number);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '00';

    // Apply Indian formatting
    $integerPartLength = strlen($integerPart);

    // Handle cases where the integer part is less than 1000
    if ($integerPartLength <= 3) {
        $formattedIntegerPart = $integerPart;
    } else {
        // Separate out the last three digits
        $lastThreeDigits = substr($integerPart, -3);
        $remainingDigits = substr($integerPart, 0, -3);

        // Format the remaining digits
        $remainingDigits = strrev($remainingDigits);
        $formattedRemainingDigits = preg_replace('/(\d{2})(?=\d)/', '$1,', $remainingDigits);
        $formattedRemainingDigits = strrev($formattedRemainingDigits);

        // Combine parts
        $formattedIntegerPart = $formattedRemainingDigits . ',' . $lastThreeDigits;
    }

    return $formattedIntegerPart . '.' . $decimalPart;
}

function convertUnitAmount($amount, $fromUnit, $toUnit)
{
    $conversionRates = [
        'piece' => 1,
        'tonne' => 1000000, // grams
        'packets' => 1,
        'kg' => 1000, // grams
        'g' => 1,
        'lb' => 453.592, // grams
        'oz' => 28.3495, // grams
        'l' => 1000, // milliliters
        'ml' => 1,
        'm' => 100, // centimeters
        'cm' => 1,
        'mm' => 0.1, // centimeters
        'ft' => 30.48, // centimeters
        'in' => 2.54 // centimeters
    ];

    if (!isset($conversionRates[$fromUnit]) || !isset($conversionRates[$toUnit])) {
        throw new Exception("Invalid unit provided");
    }

    $amountInBaseUnit = $amount / $conversionRates[$fromUnit];
    $convertedAmount = $amountInBaseUnit * $conversionRates[$toUnit];

    return $convertedAmount;
}

function convertUnitQuantity($quantity, $fromUnit, $toUnit)
{
    $conversionRates = [
        'piece' => 1,
        'tonne' => 1000000, // grams
        'packets' => 1,
        'kg' => 1000, // grams
        'g' => 1, // grams
        'lb' => 453.592, // grams
        'oz' => 28.3495, // grams
        'l' => 1000, // milliliters
        'ml' => 1, // milliliters
        'm' => 100, // centimeters
        'cm' => 1, // centimeters
        'mm' => 0.1, // centimeters
        'ft' => 30.48, // centimeters
        'in' => 2.54 // centimeters
    ];

    // Validate units
    if (!isset($conversionRates[$fromUnit]) || !isset($conversionRates[$toUnit])) {
        throw new Exception("Invalid unit provided");
    }

    // Convert the quantity to the base unit
    $quantityInBaseUnit = $quantity * $conversionRates[$fromUnit];

    // Convert from the base unit to the target unit
    $convertedQuantity = $quantityInBaseUnit / $conversionRates[$toUnit];

    return $convertedQuantity;
}

function calculateAge($dob)
{
    // Convert the date of birth to a DateTime object
    $birthDate = new DateTime($dob);
    // Get today's date
    $today = new DateTime();
    // Calculate the difference between today and the date of birth
    $age = $today->diff($birthDate)->y;
    return $age;
}


//15. get rounded value
function getRoundedValue($value)
{
    return round($value, 2);
}

function getNumberSuffix($number)
{
    $last_digit = $number % 10;
    $last_two_digits = $number % 100;

    if ($last_two_digits >= 11 && $last_two_digits <= 13) {
        return 'th';
    }

    switch ($last_digit) {
        case 1:
            return 'st';
        case 2:
            return 'nd';
        case 3:
            return 'rd';
        default:
            return 'th';
    }
}
