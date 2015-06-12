<?php
//ini_set('max_execution_time', 60);
set_time_limit(0);

// Function to make GET request using cURL
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

// Function to return XPath object
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument(); // Instantiating a new DomDocument object
	@$xmlPageDom->loadHTML($item); // Loading the HTML from downloaded page
	$xmlPageXPath = new DOMXPath($xmlPageDom); // Instantiating new XPath DOM object
	return $xmlPageXPath; // Returning XPath object
}

$packtBook = array(); // Declaring array to store scraped book data.

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

//Set Properties
echo date('H:i:s') . " Set properties<br />";
$objPHPExcel->getProperties()->setCreator("Vatsal");
$objPHPExcel->getProperties()->setLastModifiedBy("Vatsal");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("Scrapped Data 99Acres.");
$objPHPExcel->setActiveSheetIndex(0);
PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

//Scrape all pages 368
for ($p = 1; $p < 368; $p++) {
$packtPage = curlGet('http://www.99acres.com/rent-property-in-gurgaon-ffid-page-'.$p.'?search_type=QS&search_location=HP&lstAcn=HP_R&src=CLUSTER&isvoicesearch=N&keyword_suggest=gurgaon%3B&fullSelectedSuggestions=gurgaon&strEntityMap=W3sidHlwZSI6ImNpdHkifSx7IjEiOlsiZ3VyZ2FvbiIsIkNJVFlfOCwgUFJFRkVSRU5DRV9SLCBSRVNDT01fUiJdfV0%3D&texttypedtillsuggestion=gur&refine_results=Y&Refine_Localities=Refine%20Localities&action=%2Fdo%2Fquicksearch%2Fsearch&suggestion=CITY_8%2C%20PREFERENCE_R%2C%20RESCOM_R');
$packtPageXpath = returnXPathObject($packtPage); // Instantiating new XPath DOM object

$author = $packtPageXpath->query('//a[@id[starts-with(., "desc_") ]]/@href');
// If authors exist
if ($author->length > 0) {
// For each author
$len=$author->length;

//$len=1;//temp
for ( $i=0; $i < $len; $i++) {
$packtBook[] = $author->item($i)->nodeValue; //Add author to 2nd dimension of array
}
}
echo "<br>";
echo 'Done with page no. = '.$p;
echo "<br>";
if($p%10==0){
//sleep(rand(1, 2));
}
}

//print_r($packtBook);
//Set Title
echo sizeof($packtBook);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "PROPID");
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "TITLE");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "PRICE");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "BEDROOMS");
$objPHPExcel->getActiveSheet()->SetCellValue('E1', "BATHROOMS");
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "SUPER BUILT-UP AREA");
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "BUILT-UP AREA");
$objPHPExcel->getActiveSheet()->SetCellValue('H1', "CARPET AREA");
$objPHPExcel->getActiveSheet()->SetCellValue('I1', "BALCONIES");
$objPHPExcel->getActiveSheet()->SetCellValue('J1', "FLOOR NUMBER");
$objPHPExcel->getActiveSheet()->SetCellValue('K1', "FACING");
$objPHPExcel->getActiveSheet()->SetCellValue('L1', "POSSESSION");
$objPHPExcel->getActiveSheet()->SetCellValue('M1', "PROPERTY AGE");
$objPHPExcel->getActiveSheet()->SetCellValue('N1', "TYPE OF FURNISHING");
$objPHPExcel->getActiveSheet()->SetCellValue('O1', "FURNISHINGS");
$objPHPExcel->getActiveSheet()->SetCellValue('P1', "DESCRIPTION");
$objPHPExcel->getActiveSheet()->SetCellValue('Q1', "FEATURES");
$objPHPExcel->getActiveSheet()->SetCellValue('R1', "DISTANCE AND LANDMARK");
$objPHPExcel->getActiveSheet()->SetCellValue('S1', "CONTACT");

for ($j=0;$j<sizeof($packtBook);$j++){
$s_count=$j+2;
echo "<hr>".$s_count."<hr>";
$packtPage = curlGet('http://www.99acres.com'.$packtBook[$j]);
$packtPageXpath = returnXPathObject($packtPage); // Instantiating new XPath DOM object
echo"<hr>";
echo 'http://www.99acres.com'.$packtBook[$j];
echo"<hr>";
echo"<br>";

 
//Title
$author = $packtPageXpath->query('//head/title');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
echo "<br>";
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$s_count, trim((string)$price));
echo $price;
$dir=str_replace("/", "-", $price);
}
else{
echo "<br>";
echo "No Title";
}


//Image
$author = $packtPageXpath->query('//a[@href[starts-with(., "/load/imagegallery") ]]/@href');
$price=$author->item(0)->nodeValue;
$packtPage3 = curlGet('http://www.99acres.com'.$price);
$packtPageXpath3 = returnXPathObject($packtPage3);
$author = $packtPageXpath3->query('//div[@id="gal_script"]');
$price=$author->item(0)->nodeValue;
$price=trim((string)$price);
$a=explode(";",$price);
//print_r ($a);
$propid=$a[0];
$propid=substr($propid,22);
$propid=chop($propid,"'");
//echo "<br><br>XXXXXXXXXXXXXXX-----X2=----->".$propid."<br><br>";
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$s_count, $propid);
//http://pics.99acres.com/image-gallery/serve-R17199778-2-FULLPHOTO
$links = array();
foreach ($a as $t){
//echo $t."<br>";
$x=strpos($t,'full_image_url:');
if($x)
$links[]=substr($t,$x);
}
$x=strpos($t,"'");
$links2 = array();
foreach ($links as $t){
//echo $t."<br>";
$x=strpos($t,"'");
if($x){
$x=$x+1;
$t=substr($t,$x);
$links2[]=chop($t,"'})");
}
}
echo "<br>";echo "<br>";
//print_r ($links2);
//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$s_count, $price);
//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$s_count, $price);
//echo $price;
//$inc1=65;
//$inc2=65;
mkdir("C:/xampp/htdocs/webscrrp/".$s_count.",".$propid."--".$dir);
foreach($links2 as $imageUrl){
$imageUrl=urldecode($imageUrl);
echo $imageUrl;
// If file is an image
if (getimagesize($imageUrl)) {
$imageFile = curlGet($imageUrl); // Download image using cURL
$name=explode("/",$imageUrl);
if(sizeof($name)>0){
$fname=$name[sizeof($name)-1];
}
//echo "<br>"."<br>"."Size:".sizeof($name)."--------Name:".$fname;
//echo "<br>"."C:/xampp/htdocs/webscrrp/".$s_count.",".$propid."--".$dir."/".$fname."<br>";
$file = fopen("C:/xampp/htdocs/webscrrp/".$s_count.",".$propid."--".$dir."/".$fname, 'w'); // Opening file handle
fwrite($file, $imageFile); // Writing image file
fclose($file); // Closing file handle
/*
$gdImage = imagecreatefromjpeg('temp.jpeg');
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('PHPExcel logo');
$objDrawing->setDescription('PHPExcel logo');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
//$objDrawing->setHeight(150);
//$objDrawing->setCoordinates('C1');
//$objDrawing->setPath('./temp.jpeg');      // filesystem reference for the image file
$objDrawing->setHeight(36);                 // sets the image height to 36px (overriding the actual image height); 
$objDrawing->setCoordinates(chr($inc1).chr($inc2).$j);    // pins the top-left corner of the image to cell D24
if($inc2==90){
$inc1=$inc1+1;
$inc2=65;
}
else
$inc2=$inc2+1;
$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/
}
else
{echo "<br>"."<br>"."Not opening"."<br><br>";}
}
$n=1;
while(1){
$imageUrl="http://pics.99acres.com/image-gallery/serve-".$propid."-".$n."-FULLPHOTO";
$imageUrl=urldecode($imageUrl);
echo $imageUrl;
// If file is an image
if (getimagesize($imageUrl)) {
$imageFile = curlGet($imageUrl); // Download image using cURL
/*$name=explode("/",$imageUrl);
if(sizeof($name)>0){
$fname=$name[sizeof($name)-1];
}*/
//echo "<br>"."<br>"."Size:".sizeof($name)."--------Name:".$fname;
//echo "<br>"."C:/xampp/htdocs/webscrrp/".$dir."/".$fname."<br>";
$file = fopen("C:/xampp/htdocs/webscrrp/".$s_count.",".$propid."--".$dir."/".$n."BV.jpeg", 'w'); // Opening file handle
fwrite($file, $imageFile); // Writing image file
fclose($file); // Closing file handle
$n++;
}
else
break;
}

//Price
$author = $packtPageXpath->query('//span[@class="redPd b"]');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,7);
echo "<br>";
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$s_count, trim((string)$price));
echo $price;}
else{
echo "<br>";
echo "No Price";
}

//BedRoom
$author = $packtPageXpath->query('//div[@class="lf"]/b');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,2);
echo "<br>";
echo $price;
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "No Bedroom";
}

//Bathroom
$author = $packtPageXpath->query('//div[@class="lf mt15"]/b');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,2);
echo "<br>";
echo $price;
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "No Bathroom";
}

//Super-Built-up-Area
$author = $packtPageXpath->query('//span[@class="lf mt5"]/i[@class="b vtop"]');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,2);
echo "<br>";
echo "SB = ".$price;
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None S-Built";
}

//Built-up-Area
$author = $packtPageXpath->query('//span[@class="lf mt5"]/i[@class="b"]');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,2);
echo "<br>";
echo $price;
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None";
}

//Carpet Area
$author = $packtPageXpath->query('//span[@class="lf mt5"]/i/b[@id="carpetArea_span"]');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$price=substr($price,2);
$price=trim((string)$price);
echo "<br>";
echo $price;
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None";
}

//Balcony
$count=0;
$author = $packtPageXpath->query('//i[@id="balcony_numLabel"]');
if ($author->length > 0 ) {
$author = $packtPageXpath->query('//i[@class="blk"]');
$price=$author->item(0)->nodeValue;
$count=$count+1;
$price=substr($price,2);
echo "<br> ";
echo "B: ".$price;
$price=trim((string)$price);
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None Bal";
}

//Floor No.
$author = $packtPageXpath->query('//i[@id="floor_numLabel"]');
if ($author->length > 0 ) {
$author = $packtPageXpath->query('//i[@id="total_floorLabel"]');
$price=$author->item(0)->nodeValue;
//$price=substr($price,2);
//$final=html_entity_decode($price);
//$final=str_replace("\t"," ",(string)$final);
$string = htmlentities((string)$price, null, 'utf-8');
$content = str_replace("&nbsp;", " ", $string);
$final = html_entity_decode($content);
$final=trim($final);
echo "<br> ";
echo "Floor no. = : ".$final;
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$s_count, trim((string)$final));
}
else{
echo "<br>";
echo "None Floor";
}

//Facing
$author = $packtPageXpath->query('//i[@id="facingLabel"]');
if ($author->length > 0 ) {
$author = $packtPageXpath->query('//i[@class="blk" or @class="blk vtop" ]');
$att = $packtPageXpath->query('//i[@class="blk" or @class="blk vtop" ]/@class');
$price=$author->item($count)->nodeValue;
echo $att->item($count)->nodeValue;
if((string)$att->item(0)->nodeValue="blk")
{$count=$count+1;}
//$price=substr($price,2);
echo "<br> ";
echo "F= ".$price;
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None Face ";
}

//Possession
$c=0;
$author = $packtPageXpath->query('//i[@class="blk" and @id="availabilityLabel"]');
if ($author->length > 0) {
$price=$author->item(0)->nodeValue;
$c=$c+1;
echo "<br>";
echo $price;
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None";
}

//Property Age
$author = $packtPageXpath->query('//i[@id="ageLabel"]');
if ($author->length > 0 ) {
$author = $packtPageXpath->query('//div[@class="lf"]/div[@class="spdp_blCny f13 fwn"]/i[@class="blk"]');
echo $author->length;
$price=$author->item($c)->nodeValue;
//$price=substr($price,2);
echo "<br> ";
echo "Age = ".$price;
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None Age ";
}

//Furnishing type
$author = $packtPageXpath->query('//div[@class="leftPane f13"]/label');
if ($author->length > 0 ) {
$price=$author->item(0)->nodeValue;
//$price=substr($price,2);
echo "<br> ";
echo "FurnshT = ".trim((string)$price);
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None FurnshType ";
}

//Furnishing
$author = $packtPageXpath->query('//div[@class="furnshAmn"]');
if ($author->length > 0 ) {
$price=$author->item(0)->nodeValue;
//$price=substr($price,2);
echo "<br> ";
echo "Furnsh = ".trim((string)$price);
$objPHPExcel->getActiveSheet()->SetCellValue('O'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None Furnsh ";
}


//Description
$author = $packtPageXpath->query('//p');
if ($author->length > 0 ) {
$price=$author->item(0)->nodeValue;
//$price=substr($price,2);
echo "<br> ";
echo "Desc = ".trim((string)$price);
$objPHPExcel->getActiveSheet()->SetCellValue('P'.$s_count, trim((string)$price));
}
else{
echo "<br>";
echo "None Desc ";
}

//Features
$str=""; 
$author = $packtPageXpath->query('//div[@class="b f13 grey1 mt5"]');
//$author2 = $packtPageXpath->query('//div[@class="ameN"]/li');
//echo "<br>leng".$author->length;
$count2=0;
if ($author->length > 0 ) {
for($r=0;$r<$author->length;$r++){
$price=$author->item($r)->nodeValue;
if(trim((string)$price)=="Home Features"){
echo trim((string)$price);echo "<br>";
$str=$str.trim((string)$price)."<br>";
$count2=$count2+1;
$author2 = $packtPageXpath->query('(//ul[@class="ameN"])['.$count2.']/li');
}
if(trim((string)$price)=="Society/ Building Features"){
echo trim((string)$price);echo "<br>";
$str=$str.trim((string)$price)."<br>";
$count2=$count2+1;
$author2 = $packtPageXpath->query('(//ul[@class="ameN"])['.$count2.']/li');
}
if(trim((string)$price)=="Other Features"){
echo trim((string)$price);echo "<br>";
$str=$str.trim((string)$price)."<br>";
$count2=$count2+1;
$author2 = $packtPageXpath->query('(//ul[@class="ameN"])['.$count2.']/li');
}

for($r2=0;$r2<$author2->length;$r2++){
$price=$author2->item($r2)->nodeValue;
echo $price."<br>";
$str=$str.trim((string)$price)."<br>";
}
}
$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$s_count, trim((string)$str));
}
else{
echo "<br>";
echo "None Feature ";
}

//Distance
$str="";
$author = $packtPageXpath->query('//ul[@class="disN"]');
if ($author->length > 0 ) {
echo $author->length;
for ($z=0;$z<$author->length;$z++){
$price=$author->item($z)->nodeValue;
echo "<br> <br>";
echo "Dis = ".$price;
$str=$str.trim((string)$price);
}
$objPHPExcel->getActiveSheet()->SetCellValue('R'.$s_count,$str);
//$price=substr($price,2);
//$objPHPExcel->getActiveSheet()->SetCellValue('L'.$s_count, $price);
}
else{
echo "<br>";
echo "None Distance ";
}


//Contact
$author = $packtPageXpath->query('//div[@class="lf f13" or @class="lf f13 lp10"]');
if ($author->length > 0 ) {
echo $author->length;
for ($z=0;$z<$author->length;$z++){
$price=$author->item($z)->nodeValue;
echo "<br> <br>";
echo "Contact = ".$price;
$objPHPExcel->getActiveSheet()->SetCellValue('S'.$s_count, trim((string)$price));
}
//$price=substr($price,2);
//$objPHPExcel->getActiveSheet()->SetCellValue('L'.$s_count, $price);
}
else{
echo "<br>";
echo "None Contact";
}

//Excel Sheet
//$objPHPExcel->getActiveSheet()->getStyle('A'.$s_count.':R'.$s_count)->getAlignment()->setWrapText(true);
foreach(range('A'.$s_count,'S'.$s_count) as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
}

//Sleep
/*echo "<br><br>Sleep<br>";
sleep(rand(1, 3));
echo "<br>Start<br>";*/
}

$objPHPExcel->getActiveSheet()->setTitle('Simple');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') . " Done writing file.\r\n";

?>