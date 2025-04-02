<?php
include_once('../../../config/sparrow.php');
include_once('../../../packages/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    
//     try {
//         // File upload logic
//         $fileTmpPath = $_FILES['event_file_upload']['tmp_name'];
//         $fileName = $_FILES['event_file_upload']['name'];
//         $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

//         // Validate the file extension
//         if (in_array($fileExtension, ['xls', 'xlsx', 'csv'])) {
//             try {
//                 // Load the uploaded Excel file
//                 $spreadsheet = IOFactory::load($fileTmpPath);
//                 $sheet = $spreadsheet->getActiveSheet();
//                 $rows = $sheet->toArray();

// print_r($rows);
// // Extract event types from the uploaded file
//                 $eventTypes = [];
//                 foreach ($rows as $row) {
//                     $eventTypes[] = $row[4]; // Assuming event type is at index 4
//                 }

//                 // Convert event types array to JSON
//                 $eventTypesJson = json_encode($eventTypes);

//                 // Fetch the matched and unmatched event types from the database
//                 $procedure_params = [
//                     ['name' => 'event_types_json', 'type' => 's', 'value' => $eventTypesJson],
//                 ];
//                 print_r($eventTypesJson);

//                 $result = callProcedure("get_event_types_and_match", $procedure_params);
// print_r($result);
// exit;
//                 // Retrieve matched and unmatched event types
//                 $matchedEventTypes = json_decode($result['matched_event_types']);
//                 $unmatchedEventTypes = json_decode($result['unmatched_event_types']);
//                 print_r($matchedEventTypes);
//                 print_r($unmatchedEventTypes);
// exit;
//                 // Return matched event types in response
//                 if ($matchedEventTypes) {
//                     echo json_encode([
//                         'code' => 200,
//                         'status' => 'success',
//                         'message' => 'Event types matched successfully',
//                         'data' => $matchedEventTypes
//                     ]);
//                 } else {
//                     echo json_encode([
//                         'code' => 404,
//                         'status' => 'error',
//                         'message' => 'No matching event types found'
//                     ]);
//                 }
//                 exit;
//             } catch (Exception $e) {
//                 echo json_encode([
//                     'code' => 600,
//                     'status' => 'error',
//                     'message' => 'Error reading Excel file: ' . $e->getMessage()
//                 ]);
//             }
//         } else {
//             echo json_encode([
//                 'code' => 400,
//                 'status' => 'error',
//                 'message' => 'Invalid file type. Only XLS, XLSX, and CSV are allowed.'
//             ]);
//             exit;
//         }
//     } 
try {
    // File upload and validation logic (already written)
    $fileTmpPath = $_FILES['event_file_upload']['tmp_name'];
    $spreadsheet = IOFactory::load($fileTmpPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    // print_r($rows);
    // print_r("<br>");
    // Convert the entire $rows array to JSON
    $rowsJson = json_encode($rows);
    // Call the procedure with the JSON array
    $procedureParams = [
        ['name' => 'event_rows_json', 'type' => 's', 'value' => $rowsJson],
    ];
    $result = callProcedure('categorize_event_types', $procedureParams);

// print_r("<br>");
// print_r("/n json encoded".$rowsJson);


    // Decode the results
    $matchedRows = json_decode($result['data'][0][0]['matched_indices'], true);
    $unmatchedRows = json_decode($result['data'][0][0]['unmatched_indices'], true);

    // Return matched and unmatched rows in the response
    echo json_encode([
        'code' => 200,
        'status' => 'success',
        'matched' => $matchedRows,
        'unmatched' => $unmatchedRows,
        'orgin' => $rows
    ]);
} catch (Exception $e) {
    echo json_encode([
        'code' => 500,
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode([
            'code' => 600,
            'status' => 'error',
            'message' => 'An error occurred.'
        ]);
    }
} else {
    echo json_encode([
        'code' => 400,
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
    exit;
}
?>
