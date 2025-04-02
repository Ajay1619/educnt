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



        $faculty_subjects_id = isset($_POST['faculty_subjects_id']) ? sanitizeInput(decrypt_data($_POST['faculty_subjects_id']), 'int') : 0;
        $attendance_date = isset($_POST['attendance_date']) ? sanitizeInput(decrypt_data($_POST['attendance_date']), 'string') : 0;
        $selected_attendance_slot = isset($_POST['selected_attendance_slot']) ? sanitizeInput(decrypt_data($_POST['selected_attendance_slot']), 'int') : 0;
        $procedure_params = [
            ['name' => 'faculty_subjects_id', 'value' => $faculty_subjects_id, 'type' => 'i'],
            ['name' => 'attendance_date', 'value' => date(DB_DATE_FORMAT, strtotime($attendance_date)), 'type' => 's'],
            ['name' => 'selected_attendance_slot', 'value' => $selected_attendance_slot, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_student_attendance_data", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0][0]['total_students'])) {
                    $data = $result['data'];
                    $total_students = $data[0][0]['total_students'];
                    $subject_details = $data[1][0];
                    $student_details = $data[2];
                    $group_details = $data[3];
                    $group_chip = "";
                    foreach ($group_details as $group_detail) {
                        $group_chip .= "<div class='chip' data-chip-id='{$group_detail['group_id']}'>{$group_detail['group_title']}</div>";
                    }
                    $table = "
                    <div class='row'>
                    <!-- Subject Dropdown -->
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-container'>
                            <input type='text' id='selected-attendance-subjects-dummy' value='{$subject_details['subject_name']}' name='selected-subjects' placeholder=' ' readonly>
                            <label class='input-label' for='selected-attendance-subjects-dummy'>Select subject</label>
                            <input type='hidden' name='selected_attendance_subject' value='{$faculty_subjects_id}'class='selected-attendance-subject-filter' id='selected-attendance-subject' required>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-container date'>
                            <input type='date' class='bulmaCalendar' id='selected-attendance-date' name='attendance_date' placeholder='dd-MM-yyyy' required readonly value='{$attendance_date}'>
                            <label class='input-label' for='selected-attendance-date'>Select The Date</label>
                        </div>
                    </div>
                    <!-- Slot Selection -->
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-container '>
                            <input type='text' class='auto selected-attendance-slots-dummy' placeholder=' ' value='{$subject_details['period_time']}' readonly>
                            <label class='input-label'>Select The Slots</label>
                            <input type='hidden' name='selected_attendance_slots' value='{$subject_details['period_id']}' id='selected-attendance-slots' class='selected-attendance-slots' readonly required>
                        </div>
                    </div>

                    <!-- Group Selection -->
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='input-container'>
                            <input type='text' class='auto selected-attendance-group-dummy' placeholder=' ' readonly>
                            <label class='input-label'>Select The Groups</label>
                            <input type='hidden' name='selected_attendance_group' class='selected-attendance-group'>
                        </div>
                        <div class='chip-container' id='selected-attendance-group-list-chips'>{$group_chip}</div>
                    </div>
                </div>
                    <table id='individual-subjectwise-attendance-table'  class='portal-table'>
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
                    foreach ($student_details as $index => $student_detail) {
                        $attendance = $student_detail['attendance']; // 1 - Present, 2 - Absent, 3 - On Duty
                        $confirmation_status = $student_detail['attendance_confirmation_status']; // 1 - None, 2 - Authorised, 3 - Unauthorised

                        $table .= "<tr>
                            <td>{$slNo}</td>
                            <td>{$student_detail['student_name']}<input type='hidden' name='attendance_student_id[]' value='{$student_detail['student_id']}'></td>
                            <td>{$student_detail['student_reg_number']}</td>
                            <td>
                                <div class='input-group'>
                                    <label class='modern-radio'>
                                        <input type='radio' name='student_attendance_status[{$index}]' value='1' " . ($attendance == 1 ? "checked" : "") . ">
                                        <span></span>
                                        <div class='modern-label'>Present</div>
                                    </label>
                                    
                                    <label class='modern-radio'>
                                        <input type='radio' name='student_attendance_status[{$index}]' value='2' " . ($attendance == 2 ? "checked" : "") . ">
                                        <span></span>
                                        <div class='modern-label'>Absent</div>
                                    </label>
                                    
                                    <label class='modern-radio'>
                                        <input type='radio' name='student_attendance_status[{$index}]' value='3' " . ($attendance == 3 ? "checked" : "") . ">
                                        <span></span>
                                        <div class='modern-label'>On Duty</div>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class='input-container dropdown-container'>
                                    <input type='text' id='selected-permission-dummy-{$index}' name='selected-permission' class='auto dropdown-input selected-permission-dummy' value='" . ($confirmation_status == 1 ? "None" : ($confirmation_status == 2 ? "Authorised" : "Unauthorised")) . "' placeholder=' ' readonly>
                                    <label class='input-label' for='selected-permission-dummy-{$index}'>Select Permission</label>
                                    <input type='hidden' name='student_attendance_permission[]' class='student-attendance-permission' id='selected-permission-{$index}' value='{$confirmation_status}'>
                                    <span class='dropdown-arrow'>&#8964;</span>
                                    <div class='dropdown-suggestions'></div>
                                </div>
                            </td>
                            <td>
                                <div class='input-container'>
                                    <input type='text' name='student_attendance_note[]' value='{$student_detail['attendance_notes']}' maxlength='100' class='auto input-text' placeholder='Enter note'>
                                </div>
                            </td>
                        </tr>";

                        $slNo++;
                    }


                    $table .= "</tbody></table><button type='submit' class='primary text-center full-width mt-6'>EDIT</button>";
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'total_students' => $total_students, 'subject_details' => $subject_details, 'table' => $table]);
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
