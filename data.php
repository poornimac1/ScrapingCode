<?php
/*
 * PHP Excel - Create a simple 2007 XLSX Excel file
 */

/** Set default timezone (will throw a notice otherwise) */
date_default_timezone_set('America/Los_Angeles');


/** PHPExcel */
include 'PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object<br />";
$objPHPExcel = new PHPExcel();

function curlGet($url) {
	$ch = curl_init(); // Initialising cURL session
	// Setting cURL options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$results = curl_exec($ch); // Executing cURL session
	curl_close($ch); // Closing cURL session
	return $results; // Return the results
}

// Set properties
echo date('H:i:s') . " Set properties<br />";
$objPHPExcel->getProperties()->setCreator("Runnable.com");
$objPHPExcel->getProperties()->setLastModifiedBy("Runnable.com");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX,generated using PHP classes.");


// Add some data
echo date('H:i:s') . " Add some data<br />";
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Name');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'asddd!');
$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');

$imageUrl='http://mediacdn.99acres.com/25/3/503008F-1346321824-Independent_Villa_-_Sector_42.jpeg';

// If file is an image
if (getimagesize($imageUrl)) {
$imageFile = curlGet($imageUrl); // Download image using cURL
$file = fopen('temp.jpeg', 'w'); // Opening file handle
fwrite($file, $imageFile); // Writing image file
fclose($file); // Closing file handle
}
/*
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('PHPExcel logo');
$objDrawing->setDescription('PHPExcel logo');
$objDrawing->setPath('');       // filesystem reference for the image file
$objDrawing->setHeight(36);                 // sets the image height to 36px (overriding the actual image height); 
$objDrawing->setCoordinates('D24');    // pins the top-left corner of the image to cell D24
$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
*/
// Rename sheet
echo date('H:i:s') . " Rename sheet<br />";
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format<br />";
/*
 * These lines are commented just for this demo purposes
 * This is how the excel file is written to the disk, 
 * but in this case we don't need them since the file was written at the first run
 */
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

// Echo done
echo date('H:i:s') . " Done writing file. 
It can be downloaded by <a href='index.xlsx'>clicking here</a>";
?>