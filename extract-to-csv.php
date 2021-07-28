<?php
//store starting time of process
$startTime = microtime(TRUE);

//setting default time zone,
@date_default_timezone_set("GMT");

//setting system variables.
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', TRUE);

//Open the source file.
$fileHandler = fopen("air-quality-data-2004-2019.csv", "r");

//Initial creation of all of the extracted files
$Station_188 = fopen('data_188.csv', 'w');
$Station_203 = fopen('data_203.csv', 'w');
$Station_206 = fopen('data_206.csv', 'w');
$Station_209 = fopen('data_209.csv', 'w');
$Station_213 = fopen('data_213.csv', 'w');
$Station_215 = fopen('data_215.csv', 'w');
$Station_228 = fopen('data_228.csv', 'w');
$Station_270 = fopen('data_270.csv', 'w');
$Station_271 = fopen('data_271.csv', 'w');
$Station_375 = fopen('data_375.csv', 'w');
$Station_395 = fopen('data_395.csv', 'w');
$Station_447 = fopen('data_447.csv', 'w');
$Station_452 = fopen('data_452.csv', 'w');
$Station_459 = fopen('data_459.csv', 'w');
$Station_463 = fopen('data_463.csv', 'w');
$Station_481 = fopen('data_481.csv', 'w');
$Station_500 = fopen('data_500.csv', 'w');
$Station_501 = fopen('data_501.csv', 'w');

//setting ordered header in each file
$CSVHeaders = array('siteID', 'ts', 'nox', 'no2', 'no', 'pm10', 'nvpm10', 'vpm10', 'nvpm2.5', 'pm2.5', 'vpm2.5', 'co', 'o3', 'so2', 'loc', 'lat', 'long');

//array of open files
$dataFile = array($Station_188,$Station_203,$Station_206,$Station_209,$Station_213,$Station_215,$Station_228,$Station_270,$Station_271,$Station_375,$Station_395,$Station_447,$Station_452,$Station_459,$Station_463,$Station_481,$Station_500,$Station_501);

//adding the header array at the start of the extracted csv
foreach($dataFile as $value)
{
    fputcsv($value, $CSVHeaders);
}

//while not at the end of the source file exacute the following
while (!feof($fileHandler)) {
	
    //reorginising and formating the data and data order in compliance to the specification.
	//gets row from source file
    $fileRow = fgets($fileHandler);

    //explode the row into differnet columns through the use of the explode function with delimiter of ';'
	[$dateAndTime,$NOx,$NO2,$NO,$siteID,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$temp,$RH,$Air_Pressure,$Location,$geo_point_2d] = explode(';', $fileRow);

	//converting date time colum into timestamp values
	$ts = strtotime($dateAndTime);
	
    //seperating the long and lat as 
	[$long, $lat] = explode(',', $geo_point_2d);

	//create array of finished orginised data
	$fileRow = array($siteID, $ts, $NOx, $NO2, $NO, $PM10, $NVPM10, $VPM10, $NVPM2_5, $PM2_5, $VPM2_5, $CO, $O3, $SO2, $Location, $long, $lat);

	//condition checking
	if ($fileRow[2] != null or $fileRow[11] != null) {
	
		//match statment to compare the first column the siteID with column in the saved row
		match($fileRow[0]) {
			'SiteID' => 0,
			'188' => fputcsv($Station_188, $fileRow),
			'203' => fputcsv($Station_203, $fileRow),				 			
			'206' => fputcsv($Station_206, $fileRow),				 			
			'209' => fputcsv($Station_209, $fileRow),				 			
			'213' => fputcsv($Station_213, $fileRow),				 			
			'215' => fputcsv($Station_215, $fileRow),				 			
			'228' => fputcsv($Station_228, $fileRow),				 			
			'270' => fputcsv($Station_270, $fileRow),				 			
			'271' => fputcsv($Station_271, $fileRow),				 			
			'375' => fputcsv($Station_375, $fileRow),				 			
			'395' => fputcsv($Station_395, $fileRow),				 			
			'447' => fputcsv($Station_447, $fileRow),				 			
			'452' => fputcsv($Station_452, $fileRow),				 			
			'459' => fputcsv($Station_459, $fileRow),				 			
			'463' => fputcsv($Station_463, $fileRow),				 			
			'481' => fputcsv($Station_481, $fileRow),				 			
			'500' => fputcsv($Station_500, $fileRow),				 
			'501' => fputcsv($Station_501, $fileRow),
		};	
	}	
}
//close the source file
fclose($fileHandler);

//loop through and close all new data files.
foreach($dataFile as $value)
{
    fclose($value);
}

//removes 'Warning: Undefined array' from the php output
ob_clean();

//outputs the exacution time of the script
echo '<p>Time Taken = '; echo microtime(true) - $startTime; echo ' seconds.';

?>