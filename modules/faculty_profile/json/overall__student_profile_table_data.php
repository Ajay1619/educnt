<?php
include_once('../../../config/sparrow.php');
require_once('../../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Read DataTables parameters
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';

        $year_of_study = isset($_POST['year_of_study']) ? sanitizeInput($_POST['year_of_study'], 'int') : 0;
        $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;
        $dept_id = !in_array($logged_role_id, $primary_roles) ? $logged_dept_id : $department;
        $section = isset($_POST['section']) ? sanitizeInput($_POST['section'], 'int') : 0;
        $academic_batch = isset($_POST['academic_batch']) ? sanitizeInput($_POST['academic_batch'], 'int') : 0;
        $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int') : 1;
        //         $department =(int)$department;
        // var_dump($department);
        // Define column names for sorting (should match DataTables column indexes)
        $columns = ['student_id', 'student_first_name', 'academic_batch_title', 'section_title', 'year_of_study_title', 'status'];

        // Get the column name to sort by, ensure $order_column is within the valid range
        if ($order_column >= 0 && $order_column < count($columns)) {
            $sort_column = $columns[$order_column];
        } else {
            $sort_column = 'student_id'; // Default sort column
        }

        // Prepare input parameters for stored procedure or SQL query
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $section],  // Section ID
            ['type' => 'i', 'value' => $year_of_study],  // Year of Study ID
            ['type' => 'i', 'value' => $dept_id],  // Department ID
            ['type' => 'i', 'value' => $logged_login_id]  // Logged-in user ID
        ];
        //  print_r($inputParams);

        // Call the stored procedure
        $response = callProcedure('fetch_overall_student_profile_table_data', $inputParams);
// print_r($response);
        // Replace this with your actual procedure
        if ($response['particulars'][0]['status_code'] == 200) {

            // Extract data from the response
            $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
            $data = isset($response['data'][0]) ? $response['data'][0] : [];
            if ($type == 1) {
                // Prepare data for DataTables
                $table_data = [];
                $s_no = $start + 1;
                if (isset($response['data'][0][0]['student_id'])) {
                    foreach ($data as $row) {
                        if ($row['student_first_name']) {
                            # code...
                        }
                        $student_name_parts = array_filter([
                            $row['student_first_name'] ?? '',   // If not defined, use empty string
                            $row['student_middle_name'] ?? '',
                            $row['student_last_name'] ?? '',
                            $row['student_initial'] ?? ''
                        ]);

                        $student_name = implode(' ', $student_name_parts);


                        // Profile picture URL (make sure to handle cases where profile_pic_path is NULL)
                        $profile_pic = !empty($row['profile_pic_path']) ? GLOBAL_PATH . '/uploads/student_profile_pic/' . $row['profile_pic_path'] : GLOBAL_PATH . '/images/profile pic placeholder.png'; // Default image if no profile picture is set

                        $student_name_display = <<<HTML
                        <div class="student-info">
                            <div class="row align-items-center">
                                <img src="{$profile_pic}" alt="Student Avatar" class="faculty-avatar-img"> 
                                <span class="student-name ml-4">{$student_name}</span>
                            </div> 
                        </div>
                        HTML;

                        $row['student_id'] = encrypt_data($row['student_id']);
                        // Status switch checkbox
                        $status_checkbox = <<<HTML
                        <div class="toggle-switch">
                            <input type="checkbox" id="toggle-{$row['student_id']}" class="toggle-input" onchange="change_student_status('{$row['student_id']}', this.checked)">
                            <label for="toggle-{$row['student_id']}" class="toggle-label">
                                <span class="toggle-inner"></span>
                            </label>
                        </div>
                    HTML;

                        $action_svg_1 = GLOBAL_PATH . '/images/svgs/eye.svg';
                        // Action buttons
                        $action_buttons = <<<HTML
                        <div class="action-buttons">
                                <img src="{$action_svg_1}" class="action-button" onclick="view_individual_student_profile('{$row['student_id']}')">
                        </div>
                    HTML;

                        // Add the row data to the table
                        $table_data[] = [
                            's_no' => $s_no++,
                            'student_name' => $student_name_display,
                            'register_number' => $row['student_reg_number'],
                            'academic_batch' => $row['academic_batch_title'],
                            'section' => $row['section_title'],
                            'year_of_study' => $row['year_of_study_title'],
                            'status' => $status_checkbox,
                            'action' => $action_buttons
                        ];
                    }
                }

                // Return the response in the expected format for DataTables
                echo json_encode([
                    'draw' => $_POST['draw'],
                    'recordsTotal' => $total_records,
                    'recordsFiltered' => $filtered_records,
                    'data' => $table_data
                ]);
            } elseif ($type == 2) {

                $dept_tittle = isset($response['data'][2][0]) ? $response['data'][2][0] : [];




                class CustomPDF extends TCPDF
                {
                    protected $last_page_flag = false;

                    public function Close()
                    {
                        $this->last_page_flag = true;
                        parent::Close();
                    }
                    // Page header
                    public function Header()
                    {

                        if ($this->page == 1) {
                            // Primary header for the first page
                            $imageFile = '../pdf/Group 849.svg';  // Replace with the actual SVG file path
                            $this->addImageToHeader($imageFile);

                            // $imageFile2 = 'Frame 3922.png';  // Replace with the actual SVG file path
                            // $this->addImageToHeader($imageFile2);

                            // Set the position for the first header text
                            $this->SetXY(20, 10); // Set x and y position for the header text
                            $this->SetFont('helvetica', 'B', 16); // Set font for the header
                            $this->SetTextColor(255, 255, 255); // Set text color to white

                            // Use a table to control exact positioning of HTML text
                            $html = '
                        <style>
                        .overlogo{
                       
                border-radius: 10px;
                margin-bottom: 2px;
            
            
                        
                        }
                        .logo {
                    width: 50px;
                    height: 50px;
             border-radius: 50%; /* Makes the image circular */
                    
            
                }
                
                        </style>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td class="overlogo"  align="left" width="5%"><img class="logo"  src="../pdf/app_round_logo.png" alt=""></td>
                                        <td align="left" width="20%"><h1>Educnt</h1></td>
                                    </tr>
                                </table>';
                            $this->writeHTML($html, true, false, true, false, '');

                            // Set position for the second line of text (body text)
                            $this->SetXY(100, 0); // Adjust x and y position for the body text
                            $this->SetFont('helvetica', '', 12); // Set font for the body text

                            // Add text "Educnt Model report"
                            $html = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="left" width="100%"><h1>Educnt Model report</h1></td>
                    </tr>
                </table>';
                            $this->writeHTML($html, true, false, true, false, '');
                        } else {
                            // Secondary header for other pages
                            $imageFile = 'Group 851.jpg';  // Replace with the actual JPG file path
                            $this->addImageToHeader2($imageFile);

                            // Set the position for the secondary header text
                            $this->SetXY(10, 1); // Adjust x and y position for the secondary header
                            $this->SetFont('helvetica', 'I', 12);
                            $html = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" width="6%"><img src="../pdf/app_round_logo.png" alt=""></td>
            <td align="left" width="20%" color="white"><h1>Educnt</h1></td>
        </tr>
    </table>';
                            $this->writeHTML($html, true, false, true, false, '');
                        }
                    }

                    // Function to handle different image types in the header
                    private function addImageToHeader($file)
                    {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                        // Set the position for the image
                        $x = 0;
                        $y = 0;
                        $w = 0;
                        $h = 0;

                        if ($ext == 'svg') {
                            $this->ImageSVG($file, $x, $y, $w, $h, '', '', '', 0, false);
                        } else {
                            // For other image formats like JPG, PNG, etc.
                            $this->Image($file, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
                        }
                    }

                    private function addImageToHeader2($file)
                    {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                        // Set the position for the image
                        $x = 0;
                        $y = 0;
                        $w = 0;
                        $h = 0;

                        if ($ext == 'svg') {
                            $this->ImageSVG($file, $x, $y, $w, $h, '', '', '', 0, false);
                        } else {
                            $this->Image($file, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
                        }
                    }

                    // Page footer (optional)
                    public function Footer()
                    {





                        // Define the signature section HTML
                        $signatureHTML = '
            <br><br>
            <table border="0" cellpadding="5" align="center" cellspacing="0" nobr="false">
                <tr>
                    <td align="center" width="20%"><img src="../pdf/signature_hod.png" alt="HOD Signature" width="50"></td>
                    <td align="center" width="20%"><img src="../pdf/signature_dean.png" alt="Dean Signature" width="50"></td>
                    <td align="center" width="20%"><img src="../pdf/signature_principal.png" alt="Principal Signature" width="50"></td>
                    <td align="center" width="20%"><img src="../pdf/signature_ca.png" alt="CA Signature" width="50"></td>
                    <td align="center" width="20%"><img src="../pdf/signature_mentor.png" alt="Mentor Signature" width="50"></td>
                </tr>
                <tr>
                    <td align="center">HOD</td>
                    <td align="center">Dean</td>
                    <td align="center">Principal</td>
                    <td align="center">CA</td>
                    <td align="center">Mentor</td>
                </tr>
            </table>';

                        // Set Y position to ensure the signature is within the footer area
                        $this->SetY(-40); // Adjust as needed based on your footer height
                        if ($this->last_page_flag) {
                            $this->SetY(-40); // Adjust as needed based on your footer height
                            // 

                            // Write the signature section
                            $this->writeHTML($signatureHTML, true, false, true, false, '');
                        } else {


                            // You can leave this empty or set a different footer for other pages
                        }


                        // Set the background color for the footer
                        $this->SetY(-6); // Set Y position for the footer
                        $this->SetFillColor(2, 37, 76); // Set the fill color to #02254C (RGB: 2, 37, 76)

                        // Set font for the footer text
                        $this->SetFont('helvetica', 'I', 8);
                        $text = " - page " . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
                        $this->Cell(0, 10, $text, 0, false, 'C', 0, '', 0, false, 'T', 'M');
                        $department = $_SESSION['svcet_educnt_faculty_dept_short_name'];
                        if ($department != null) {
                            $dept = "(" . $_SESSION['svcet_educnt_faculty_dept_short_name'] . ")";
                        } else {
                            $dept = "";
                        }

                        $name = $_SESSION['svcet_educnt_faculty_first_name'] . $_SESSION['svcet_educnt_faculty_middle_name'] . $_SESSION['svcet_educnt_faculty_last_name'] . " " . $_SESSION['svcet_educnt_faculty_initial'] . $dept;
                        // Define the HTML content for the footer
                        $html = '
        <div style=" color:white; padding:0px; ">
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td align="left" width="50%"><b>' . $name . $dept . ' </b></td>
                    <td align="right" width="50%">Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . '</td>
                </tr>
            </table>
        </div>';
                        $Footer = '../pdf/Footer.png';  // Replace with the actual SVG file path
                        $this->addImageToFooter($Footer);






                        // Write the HTML content for the footer
                        $this->writeHTML($html, true, false, true, false, '');
                    }
                    private function addImageToFooter($file)
                    {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                        // Set the position for the image
                        $x = 0;
                        $y = 291;
                        $w = 210;
                        $h = 10;

                        if ($ext == 'svg') {
                            $this->ImageSVG($file, $x, $y, $w, $h, '', '', '', 0, false);
                        } else {
                            // For other image formats like JPG, PNG, etc.
                            $this->Image($file, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
                        }
                    }
                }


                // Create PDF instance
                $pdf = new CustomPDF();
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Author');
                $pdf->SetTitle('EDU-CNT Report');
                $pdf->setMargins(3.5, 20, 3.5);
                $pdf->AddPage();
                $pdf->SetXY(3.5, 30);
                $pdf->SetFont('helvetica', '', 12);
                $logo = '../pdf/logo.png';  // Replace with the actual JPG file path
                // $this->addImageToHeader2($imageFile);

                $particulars = '


            <table border="0" cellpadding="1" align="center" cellspacing="0" nobr="false">
             <tr>
              <th colspan="4" align="center"><img width="400px" src="' . $logo . '" alt=""></th>
             </tr>
             <tr>
              <th colspan="4" align="center"><h3>Department of Computer Science and Engineering</h3></th>
             </tr>
             <tr>
              <th colspan="4" align="center"><h4>Academic Year : 2024-2025 (Odd Sem)</h4></th>
             </tr>
             <tr>
              <th colspan="4" align="center"><h4>CST63-Database Management System</h4></th>
             </tr>
             <br>
                <tr>
               
                <th colspan="2" align="left">Head of the Department : Dr.N.Balaji</th>
                <th colspan="2" align="right">Subject Staff: Mr.S.Karthikeyen</th>
                
                
                
                </tr>
                <tr>
                <th colspan="2" align="left">Class Advisor : Mr.Suresh</th>
                <th colspan="2" align="right">Total No of Student :90</th>
                </tr>
                <tr>
                <th colspan="2" align="left">Year :IV</th>
                <th colspan="2" align="right">Semester:VIII</th>
                </tr>
                <tr>
                <th colspan="2" align="left">Group/section : V/A </th>
                <th colspan="2" align="right">Batch:2021-2024</th>
                </tr>
                
             </table>
            
            
            ';
                $Consolidate = '
<br>
<br>
<br>
<br>
<br>
<br>
<style>
table{

border: 1px solid white;
border-collapse: collapse
}
th {
    background-color:#00356F;
    color:white;
}
.rowhead {
    background-color:#00356F;
    color:white;
}
    

</style>

<table border="1" cellpadding="5" align="center" cellspacing="0" nobr="false">
<tr>
<th colspan="1" align="center">Sl.No</th>
<th colspan="1" align="center">Number Of Students</th>
<th colspan="1" align="center">Number Of OD</th>
<th colspan="1" align="center">Total No of Present</th>
<th colspan="1" align="center">Total No of Absent</th>
</tr>
<tr>
<td class="rowhead"  colspan="1" align="center">No of Boys</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
</tr>
<tr>
<td class="rowhead" colspan="1" align="center">No of Boys</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
</tr>
<tr>
<td class="rowhead" colspan="1" align="center">Total No of Student</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
<td colspan="1" align="center">16</td>
</tr>
</table>
';
                $particulars = '
<table border="0" cellpadding="1" align="center" cellspacing="0" nobr="false">
<tr>
<th colspan="4" align="center"><img width="400px" src="' . $logo . '" alt=""></th>
</tr>';
                if ($dept_tittle["Department"] == NULL) {
                    $particulars .= '
<tr>
<th colspan="4" align="center"><h3> Sri Venkateshwaraa College of Engineering and Technology - Faculty Details</h3></th>
</tr>';
                } else {
                    $particulars .= '
<tr>
<th colspan="4" align="center"><h3>Department of ' . $dept_tittle["Department"] . '</h3></th>
</tr>';
                }
                $particulars .= '

<tr>
<th colspan="4" align="center"><h4>Academic Year : 2024-2025 (Odd Sem)</h4></th>
</tr>';

                if ($dept_id == 1) {
                    $particulars .= '
<tr>
    <th colspan="2" align="left">Head of the Department : Dr.N.Balaji</th>
</tr>';
                }

                $particulars .= '
</table>';

                // Table header
                $table = '<table border="0" cellpadding="2" align="center" cellspacing="0" width="100%">
<thead>
    <tr style="background-color: #00356F; color: white;">
        <th>SL.NO</th>
        <th>Fculty Name</th>
        <th>Department</th>
        <th>Designation</th>
        
    </tr>
</thead>
<tbody>';

                $students = $data;

                foreach ($students as $index => $student) {
                    $faculty_name = $student['faculty_salutation'] . ' ' . $student['faculty_first_name'] . ' ' . $student['faculty_middle_name'] . ' ' . $student['faculty_last_name'];
                    $bgColor = ($index % 2 == 0) ? '#f2f2f2' : '#eaf0f7'; // Alternate row colors
                    $table .= '<tr style="background-color: ' . $bgColor . ';">
                    <td>' . ($index + 1) . '</td>
                    <td>' . $faculty_name . '</td>
                    <td>' . $student['dept_short_name'] . '</td>
                    <td>' . $student['designation'] . '</td>
                   
                </tr>';
                }

                $table .= '</tbody></table>';


                $Signature = '
<style>
table{

border: 1px solid white;
border-collapse: collapse
}
th {
    background-color:#00356F;
    color:white;
}
.rowhead {
    background-color:#00356F;
    color:white;
}
    

</style>
<br>
<br>
<br>
<br>
<br>
<br>
<table border="1" cellpadding="5" align="center" cellspacing="0" nobr="false">
<tr>
<th colspan="1" align="center">Faculty</th>
<th colspan="1" align="center">Signature</th>
</tr>
<tr>
<td colspan="1" align="center">hod</td>
<td colspan="1" align="center"></td>
</tr>
<tr>
<td colspan="1" align="center">dean</td>
<td colspan="1" align="center"></td>
</tr>
<tr>
<td colspan="1" align="center">principel</td> 
<td colspan="1" align="center"></td> 
</tr>
<tr>
<td colspan="1" align="center">CA</td>
<td colspan="1" align="center"></td>
</tr>
<tr>
<td colspan="1" align="center">Mentor</td>
<td colspan="1" align="center"></td>
</tr>
</table>';


                $pdf->writeHTML($particulars, true, false, true, false, '');
                $pdf->writeHTML($table, true, false, true, false, '');

                // $pdf->writeHTML($Consolidate, true, false, true, false, '');

                // $pdf->writeHTML($Signature, true, false, true, false, '');


                // Output PDF
                $pdf->Output('educnt_report.pdf', 'I');
                echo json_encode($result);
            }
        } else {
            // Handle errors if any
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No data found']);
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
}
