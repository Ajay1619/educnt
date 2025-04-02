<?php
// Include the TCPDF library
require_once('../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

// Create a new TCPDF object
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Funky PDF Example');
$pdf->SetSubject('Funky Style in PDF');
$pdf->SetKeywords('TCPDF, PDF, funky, example, test');

// Set margins to 0 for full-screen content
$pdf->SetMargins(0, 0, 0); // Remove all margins
$pdf->SetAutoPageBreak(FALSE, 0); // Disable automatic page breaks

// Set default font and add a page
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

// Set A4 page size without margins
$pdf->setPageFormat('A4', 'P'); // A4 size and portrait mode

// Define the HTML content with CSS including a funky design
$html = '
<style>
    body { font-family: sans-serif; }
    h1 { 
        color: #FF5733; 
        font-size: 28px; 
        text-align: center; 
        font-family: Impact, sans-serif;
        letter-spacing: 5px;
        text-transform: uppercase;
    }
    .content { 
        padding: 10px;
        background-color: #E5FFCC;
        text-align: justify;
        margin-bottom: 20px;
    }
    .highlight {
        color: #C70039;
        font-weight: bold;
        font-size: 16px;
        background-color: #FFC300;
        padding: 3px 10px;
        border-radius: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 14px;
    }
    table, th, td {
        border: 2px solid #333;
    }
    th, td {
        padding: 10px;
        text-align: center;
        background-color: #FFDA79;
        color: #333;
        font-family: "Comic Sans MS", cursive, sans-serif;
    }
    th {
        background-color: #FF5733;
        color: white;
        font-size: 18px;
        letter-spacing: 2px;
    }
</style>

<!-- Add an image -->
<img src="../global/images/404 not found 3.png" alt="Funky Image" >

<h1>Welcome to Funky PDF!</h1>

<div class="content">
    <p>This is a PDF with a super <span class="highlight">funky</span> style, using <b>TCPDF</b> to generate it. Below is a funky table.</p>
</div>

<!-- Create a funky table -->
<table>
    <tr>
        <th>Item</th>
        <th>Description</th>
        <th>Price</th>
    </tr>
    <tr>
        <td>1</td>
        <td>Funky Hat</td>
        <td>$15.99</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Groovy Shoes</td>
        <td>$45.50</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Disco Glasses</td>
        <td>$9.99</td>
    </tr>
</table>
';

// Write HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF as a file (or send it to the browser)
$pdf->Output('funky_example.pdf', 'D'); // Use 'I' to send to browser, 'D' to download
