<?php
require '../packages/vendor/autoload.php'; // Ensure Composer autoloader is included

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the title of the sheet
$sheet->setTitle('Styled Data');

// Add a title at the top of the sheet and merge cells for the title
$sheet->setCellValue('A1', 'Funky Product List');
$sheet->mergeCells('A1:C1');

// Style the title
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['argb' => 'FFFFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF4CAF50'],
    ],
]);

// Set headers
$sheet->setCellValue('A2', 'Item');
$sheet->setCellValue('B2', 'Description');
$sheet->setCellValue('C2', 'Price');

// Style headers
$sheet->getStyle('A2:C2')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF2196F3'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
]);

// Write some data to the spreadsheet
$data = [
    ['1', 'Funky Hat', '15.99'],
    ['2', 'Groovy Shoes', '45.50'],
    ['3', 'Disco Glasses', '9.99'],
];

// Populate the sheet with data
$row = 3; // Starting from row 3
foreach ($data as $item) {
    $sheet->setCellValue("A{$row}", $item[0]);
    $sheet->setCellValue("B{$row}", $item[1]);
    $sheet->setCellValue("C{$row}", $item[2]);

    // Apply styling to each row
    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ]);

    // Apply background color based on odd/even row
    $bgColor = ($row % 2 == 0) ? 'FFF1F8E9' : 'FFB2EBF2';
    $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);

    $row++;
}

// Add an image to the spreadsheet
$drawing = new Drawing();
$drawing->setName('Funky Logo');
$drawing->setDescription('This is the logo image');
$drawing->setPath('../global/images/Designer (4).png'); // Path to your image file
$drawing->setHeight(80); // Height in pixels
$drawing->setCoordinates('D1'); // Set the image position
$drawing->setOffsetX(10);
$drawing->setOffsetY(10);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

// Set headers to trigger download in the browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="styled_data.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
