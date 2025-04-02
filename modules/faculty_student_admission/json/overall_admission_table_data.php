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
        $admission_type = isset($_POST['admission_type']) ? sanitizeInput($_POST['admission_type'], 'int') : NULL;
        $admission_status = isset($_POST['admission_status']) ? sanitizeInput($_POST['admission_status'], 'int') : NULL;
        $admission_year = isset($_POST['admission_year']) ? sanitizeInput($_POST['admission_year'], 'int') : NULL;
        $academic_batch = isset($_POST['academic_batch']) ? sanitizeInput($_POST['academic_batch'], 'int') : NULL;
        $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int') : NULL;
        // Define column names for sorting (should match DataTables column indexes)
        $columns = ['student_id', 'student_first_name', 'student_admission_type', 'student_admission_category', 'admission_status'];

        // Determine the column name for sorting
        $sort_column = ($order_column >= 0 && $order_column < count($columns)) ? $columns[$order_column] : 'student_id';

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 'i', 'value' => $admission_status],
            ['type' => 'i', 'value' => 0],
            ['type' => 'i', 'value' => null],
            ['type' => 'i', 'value' => $logged_login_id]
        ];
        // Call the new procedure
        $response = callProcedure('fetch_pr_student_tables_admission', $inputParams);
        if ($response['particulars'][0]['status_code'] == 200) {
            // Process and return the data
            $total_records = isset($response['data'][1][0]['total_records']) ? intval($response['data'][1][0]['total_records']) : 0;
            $filtered_records = isset($response['data'][1][0]['filtered_records']) ? intval($response['data'][1][0]['filtered_records']) : 0;
            $data = isset($response['data'][0]) ? $response['data'][0] : [];
            if ($type == 1) {
                $table_data = [];
                $s_no = $start + 1;
                $action_svg_1 = GLOBAL_PATH . '/images/svgs/eye.svg';
                if (isset($response['data'][0][0]['student_admission_student_id'])) {
                    foreach ($data as $row) {
                        $encrypted_id = encrypt_data($row['student_admission_student_id']);
                        $student_name = $row['student_first_name'] . ' ' . $row['student_middle_name'] . ' ' . $row['student_last_name'];
                        if ($row['status'] ==  1) {
                            $action_buttons = "
                        <div class='action-buttons'>
                                <img src='{$action_svg_1}' class='action-button' 
                                     onclick='view_individual_student_admission(\"{$encrypted_id}\")' alt='View Faculty Profile'>  
                            </div>
                           
                        
                        ";
                        } else if ($row['status'] ==  0) {
                            $action_buttons = "
                        <div class='row text-right'>
                            <!-- View Student Details -->
                            
                            <!-- Faculty Profile View -->
                            <div class='action-buttons'>
                                <img src='{$action_svg_1}' class='action-button' 
                                     onclick='view_individual_student_admission(\"{$encrypted_id}\")' alt='View Faculty Profile'>
                            </div>
                            
                            <!-- Status Icons -->
                            <div class='status-icons action-buttons'";
                            $action_buttons .= " id='action-button'>";

                            if ($logged_role_id == 13) {
                                // Only show the action button div if the logged_role_id is 13
                                $action_buttons .= "
                            <span class='action-button' width='24' height='24' onclick='student_admission_edit({$row['student_admission_student_id']})'>✎</span>
                            <img class='action-button' src='" . GLOBAL_PATH . "/images/svgs/application_icons/user-tick.svg' alt='Accept' 
                                 width='24' height='24' id='approve-popup-admission' 
                                 onclick='student_admission_admitted({$row['student_admission_student_id']})'>
                            
                            <img class='action-button' src='" . GLOBAL_PATH . "/images/svgs/application_icons/user-remove.svg' alt='Decline' 
                                 width='24' height='24' 
                                 onclick='student_admission_declined({$row['student_admission_student_id']})'>
                        </div>";
                            } else {
                                $action_buttons .= ">";
                            }
                            $action_buttons .= "
                        </div>
                    ";
                        } else {
                            $action_buttons = "  Discontinued ";
                        }




                        $table_data[] = [
                            's_no' => $s_no++,
                            'student_first_name' => $student_name,
                            'student_admission_type' => $row['admission_type'],
                            'student_admission_category' => $row['admission_method'],
                            'admission_status' => $row['admission_status'],
                            'action' => $action_buttons
                        ];
                        // print_r($table_data);
                    }
                }


                echo json_encode([
                    "draw" => intval($_POST['draw']),
                    "recordsTotal" => $total_records,
                    "recordsFiltered" => $filtered_records,
                    "data" => $table_data
                ]);
            } elseif ($type == 2) {

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
                                    </table>
                            ';
                            $this->writeHTML($html, true, false, true, false, '');

                            // Set position for the second line of text (body text)
                            $this->SetXY(100, 0); // Adjust x and y position for the body text
                            $this->SetFont('helvetica', '', 12); // Set font for the body text

                            // Add text "Educnt Model report"
                            $html = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left" width="100%"><h1>Admission Report</h1></td>
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


                // Table header
                $table = '<table border="0" cellpadding="2" align="center" cellspacing="0" width="100%">
<thead>
    <tr style="background-color: #00356F; color: white;">
        <th>SL.NO</th>
        <th>Student Name</th>
        <th>Admission Type</th>
        <th>Admission Method</th>
        <th>Admission Status</th>
        
    </tr>
</thead>
<tbody>';

                $students = $data;

                foreach ($students as $index => $student) {
                    $student_name =  $student['student_first_name'] . ' ' . $student['student_middle_name'] . ' ' . $student['student_last_name'] . ' ' . $student['student_initial'];
                    $bgColor = ($index % 2 == 0) ? '#f2f2f2' : '#eaf0f7'; // Alternate row colors
                    $table .= '<tr style="background-color: ' . $bgColor . ';">
                    <td>' . ($index + 1) . '</td>
                    <td>' . $student_name . '</td>
                    <td>' . $student['admission_type'] . '</td>
                    <td>' . $student['admission_method'] . '</td>
                    <td>' . $student['admission_status'] . '</td>
                   
                </tr>';
                }

                $table .= '</tbody></table>';

                $pdf->writeHTML($table, true, false, true, false, '');

                // $pdf->writeHTML($Consolidate, true, false, true, false, '');

                // $pdf->writeHTML($Signature, true, false, true, false, '');


                // Output PDF
                $pdf->Output('educnt_report.pdf', 'I');
                echo json_encode($result);
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Failed to fetch student data.']);
        }
    } catch (Exception $e) {
        echo json_encode(['code' => 500, 'status' => 'error', 'message' => $e->getMessage()]);
    }
}
