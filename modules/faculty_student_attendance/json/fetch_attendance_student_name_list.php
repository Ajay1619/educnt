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

        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int') : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int') : 0;
        $group_id = isset($_POST['group_id']) ? sanitizeInput($_POST['group_id'], 'int') : [];
        $procedure_params = [
            ['name' => 'year_of_study_id', 'value' => $year_of_study_id, 'type' => 'i'],
            ['name' => 'section_id', 'value' => $section_id, 'type' => 'i'],
            ['name' => 'group_id', 'value' => json_encode($group_id), 'type' => 's'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_attendance_student_name_list", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0])) {
                    $data = $result['data'];
                    $table = "
                    <table class='portal-table'>
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Name</th>
                                <th>Register Number</th>
                                <th>Status</th>
                                <th>Permission</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>";

                    $slNo = 1;
                    $index = 0;
                    foreach ($data as $group) {
                        foreach ($group as $student) {
                            $table .= "<tr>
                                <td>{$slNo}</td>
                                <td>{$student['title']}<input type='hidden' name='attendance_student_id[]' value='{$student['student_id']}'></td>
                                <td>{$student['student_reg_number']}</td>
                                <td>
                                    <div class='input-group'>
                                        <label class='modern-radio'>
                                            <input type='radio' name='student_attendance_status[{$index}]' value='1' checked>
                                            <span></span>
                                            <div class='modern-label'>Present</div>
                                        </label>
                                        
                                        <label class='modern-radio'>
                                            <input type='radio' name='student_attendance_status[{$index}]' value='2'>
                                            <span></span>
                                            <div class='modern-label'>Absent</div>
                                        </label>
                                        
                                        <label class='modern-radio'>
                                            <input type='radio' name='student_attendance_status[{$index}]' value='3'>
                                            <span></span>
                                            <div class='modern-label'>On Duty</div>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class='input-container dropdown-container'>
                                        <input type='text' id='selected-permission-dummy' name='selected-permission' class='auto dropdown-input selected-permission-dummy' value='None' placeholder=' ' readonly>
                                        <label class='input-label' for='selected-permission-dummy'>Select Permission</label>
                                        <input type='hidden' name='student_attendance_permission[]' class='student-attendance-permission' id='selected-permission' value='1' >
                                        <span class='dropdown-arrow'>&#8964;</span>
                                        <div class='dropdown-suggestions'></div>

                                    </div>
                                </td>
                                <td>
                                    <div class='input-container'>
                                        <input type='text' name='student_attendance_note[]' maxlength='100' class='auto input-text' placeholder='Enter note'>
                                    </div>
                                </td>
                            </tr>";
                            $slNo++;
                            $index++;
                        }
                    }

                    $table .= "</tbody></table>
                    <button type='submit' class='primary text-center full-width mt-6'>SUBMIT</button>";
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'data' => $data, 'table' => $table]);
                    exit;
                } else {
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No Student data found with your Filter.']);
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
