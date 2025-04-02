<?php
include_once('../../config/sparrow.php');

require_once('../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

class CustomPDF extends TCPDF
{
    protected $last_page_flag = false;
  
	public function Close() {
	  $this->last_page_flag = true;
	  parent::Close();
	}
    // Page header
    public function Header()
    {

        if ($this->page == 1) {
            // Primary header for the first page
            $imageFile = 'Group 849.svg';  // Replace with the actual SVG file path
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
                            <td class="overlogo"  align="left" width="5%"><img class="logo"  src="app_logo.png" alt=""></td>
                            <td align="left" width="20%"><h1>Educnt</h1></td>
                        </tr>
                    </table>';
// print_r($html);

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
                <td align="left" width="6%"><img src="app_logo.png" alt=""></td>
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

        if ($ext === 'svg') {
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

        if ($ext === 'svg') {
            $this->ImageSVG($file, $x, $y, $w, $h, '', '', '', 0, false);
        } else {
            $this->Image($file, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
    }

    // Page footer (optional)
   

    
    private function addImageToFooter($file)
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        // Set the position for the image
        $x = 0;
        $y = 291;
        $w = 210;
        $h = 10;

        if ($ext === 'svg') {
            $this->ImageSVG($file, $x, $y, $w, $h, '', '', '', 0, false);
        } else {
            // For other image formats like JPG, PNG, etc.
            $this->Image($file, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
    }
   
    public function Footer()
{
    $signatureHTML = '
    <br><br>
    <table border="0" cellpadding="5" align="center" cellspacing="0" nobr="false">
        <tr>
            <td align="center" width="20%"><img src="signature_hod.png" alt="HOD Signature" width="50"></td>
            <td align="center" width="20%"><img src="signature_dean.png" alt="Dean Signature" width="50"></td>
            <td align="center" width="20%"><img src="signature_principal.png" alt="Principal Signature" width="50"></td>
            <td align="center" width="20%"><img src="signature_ca.png" alt="CA Signature" width="50"></td>
            <td align="center" width="20%"><img src="signature_mentor.png" alt="Mentor Signature" width="50"></td>
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

if ($this->last_page_flag) {
    $this->SetY(-40); // Adjust as needed based on your footer height
    // 
    
    // Write the signature section
    $this->writeHTML($signatureHTML, true, false, true, false, '');
  } else {
   

    // You can leave this empty or set a different footer for other pages
  }
  
    $this->SetY(-6); // Set Y position for the footer
    $this->SetFillColor(2, 37, 76); // Set the fill color to #02254C (RGB: 2, 37, 76)

    // Set font for the footer text
    $this->SetFont('helvetica', 'I', 8);
    $text = "My Footer - Page " . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
    $this->Cell(0, 10, $text, 0, false, 'C', 0, '', 0, false, 'T', 'M');

    // Define the HTML content for the footer
    $html = '
        <div style="color:white; padding:0px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td align="left" width="50%"><b>Thiruvarasan.M (IT)</b></td>
                    <td align="right" width="50%">Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . '</td>
                </tr>
            </table>
        </div>';
    
    // Add footer background image
    $Footer = 'Footer.png';  // Replace with the actual image path
    $this->addImageToFooter($Footer);

    // Write the HTML content for the footer
    $this->writeHTML($html, true, false, true, false, '');

    // Add the signature section unconditionally
    // Adjust as needed based on your footer height

    // Write the signature section
    // $this->writeHTML($signatureHTML, true, false, true, false, '');
}

}


// Create PDF instance
$pdf = new CustomPDF();
$pdf->SetCreator('Educnt Producer v1.0'); // Custom PDF producer
$pdf->SetAuthor('Educnt Application');   // Custom application name
$pdf->SetTitle('EDU-CNT Report');        // Title of the PDF
$pdf->SetSubject($dept.'Faculty Profile Report'); // Subject of the document
$pdf->SetKeywords('Faculty Profile, Educnt, College'); // Keywords

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Educnt');
$pdf->setSubject($dept.'-Faculty Profile');
$pdf->setKeywords('Faculty ProFile');
$pdf->SetTitle('EDU-CNT Report');

$pdf->setMargins(3.5, 20, 3.5);
$pdf->AddPage('P', 'A4');
$pdf->SetXY(3.5, 30);
$pdf->SetFont('helvetica', '', 12);
$logo = 'logo.png';  // Replace with the actual JPG file path
// $this->addImageToHeader2($imageFile);


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
// Table header
$table = '<table border="1" cellpadding="2" align="center" cellspacing="0" width="100%">
    <thead>
        <tr style="background-color: #00356F; color: white;">
            <th>SL.NO</th>
            <th>Student Name</th>
            <th>CIA-I</th>
            <th>CIA-II</th>
            <th>2 Mark</th>
            <th>Modal Exam</th>
            <th>Sem Result</th>
        </tr>
    </thead>
    <tbody>';
   
$students = [
    ['thiruvarasan.V', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    ['Ragul.N', 45, 65, 85, 45, 'S'],
    // Add other students here...
];

foreach ($students as $index => $student) {
    $bgColor = ($index % 2 === 0) ? '#f2f2f2' : '#eaf0f7'; // Alternate row colors
    $table .= '<tr style="background-color: ' . $bgColor . ';">
                        <td>' . ($index + 1) . '</td>
                        <td>' . $student[0] . '</td>
                        <td>' . $student[1] . '</td>
                        <td>' . $student[2] . '</td>
                        <td>' . $student[3] . '</td>
                        <td>' . $student[4] . '</td>
                        <td>' . $student[5] . '</td>
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

$pdf->writeHTML($Consolidate, true, false, true, false, '');

$pdf->writeHTML($Signature, true, false, true, false, '');



$pdf->lastPage();
// Output PDF
$pdf->Output('educnt_report.pdf', 'I');

