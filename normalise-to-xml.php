<?php
//store starting time of process
$startTime = microtime(TRUE);

//array of data file names
$station_list = array('data_188', 'data_203', 'data_206', 'data_209', 'data_213', 'data_215', 'data_228', 'data_270', 'data_271', 'data_375', 'data_395', 'data_447', 'data_452', 'data_459', 'data_463', 'data_481', 'data_500', 'data_501');

//Function for Conversion
function Normilise($SourceCSVFile, $CreatedXMLFile) {
	
	//Open the source file.
	$inputtedCSVFile  = fopen($SourceCSVFile, 'rt');
	
	//setting the encoding of the xml file and creating a new document.
	$XMLFile  = new DomDocument('1.0',"UTF-8");
	$XMLFile->formatOutput = true;
	
	//getting the first line of the csv file to skip headers
	$FileRow = fgets($inputtedCSVFile);

	//getting first line value to be Normalised
	$FileRow = fgets($inputtedCSVFile);

    //exploding the row sto red to identifiy individual elements as columns
	[$siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long] = explode(',', $FileRow);

    //replaceing the FileRow from a string to an array of elements.
	$FileRow = array($siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long);
	
	//creating the root element for the station.
	$root = $XMLFile->createElement('station');
	
    //checks for empty values in the siteID column
	if ($FileRow[0] != null){
       $attribute = $XMLFile -> createAttribute('id'); //create new atribite of 'id'
        $attribute -> value = $FileRow[0]; //set atributes value to the value of the siteID
		$root -> appendChild($attribute);
	}
	
	//Creating the element of name for the station record
	if ($FileRow[14] != null){
		$attribute = $XMLFile -> createAttribute('name');
		
		//converting '&' symbol to '$amp' for veiwing on web	
		$FileRow[14] = str_replace('&', '&amp;', $FileRow[14]);
        $FileRow[14] = str_replace('"', '', $FileRow[14]);
        $attribute -> value = $FileRow[14];
    	$root -> appendChild($attribute);
	}
	
	//checks if there is a geocode in the line
	if ($FileRow[15] != null){
		$attribute = $XMLFile -> createAttribute('geocode'); // creates a new attribute of station for coordanates 

		$InvalidCharacterCatch = $FileRow[15].','.$FileRow[16]; //checking that the coordinates have been successfully exploded into lat and long
		$StationGeocode = substr($InvalidCharacterCatch,0,-1);
		$attribute -> value = $StationGeocode;
		$root -> appendChild($attribute); // append to root
	}
	
	//creation of the record now that the root has benn generated 
	$XMLFile -> appendChild($root);

	if ($FileRow[2] != null) {
		//create new child element of stations
		$child = $XMLFile -> createElement('rec');
	
		$childAttrabutes = $XMLFile -> createAttribute('ts'); //creating a timestamp attribute
		$childAttrabutes -> value = $FileRow[1]; //getting the value from the correct varible in the line
		$child -> appendChild($childAttrabutes);
		
		$childAttrabutes = $XMLFile -> createAttribute('nox'); //creating a nox attribute
		$childAttrabutes -> value = $FileRow[2]; //getting the value from the correct varible in the line
		$child -> appendChild($childAttrabutes);
		
		if ($FileRow[3] != null){
			$childAttrabutes = $XMLFile -> createAttribute('no'); //creating a no attribute
			$childAttrabutes -> value = $FileRow[3]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		}
		
		if ($FileRow[4] != null){
			$childAttrabutes = $XMLFile -> createAttribute('no2'); //creating a no2 attribute
			$childAttrabutes -> value = $FileRow[4]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		}
		//append the child to the root
		$root -> appendChild($child);



	}
	
	while (($FileRow = fgets($inputtedCSVFile)) !== FALSE)
	{
	
		//exploding the file row of each extracted csv file
		[$siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long] = explode(',', $FileRow);
		//assigning in an array
		$FileRow = array($siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long);
		
		//checks if there is data in the 3rd column before continuing.
		if ($FileRow[2] != null) {
            //create new child element of stations
			$child = $XMLFile -> createElement('rec');
	
			$childAttrabutes = $XMLFile -> createAttribute('ts'); //creating a timestamp attribute
			$childAttrabutes -> value = $FileRow[1]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		
			$childAttrabutes = $XMLFile -> createAttribute('nox'); //creating a nox attribute
			$childAttrabutes -> value = $FileRow[2]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		
		if ($FileRow[3] != null){
			$childAttrabutes = $XMLFile -> createAttribute('no'); //creating a no attribute
			$childAttrabutes -> value = $FileRow[3]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		}
		
		if ($FileRow[4] != null){
			$childAttrabutes = $XMLFile -> createAttribute('no2'); //creating a no2 attribute
			$childAttrabutes -> value = $FileRow[4]; //getting the value from the correct varible in the line
			$child -> appendChild($childAttrabutes);
		}
		
		}
		//appending the rest of the child root elements to the xml
		$root -> appendChild($child);
		
	}
	
	//veriable for ensuring that the xml format is used
	$strxml = $XMLFile->saveXML();
	
	$exportableXML = fopen($CreatedXMLFile, "w"); //open the xml file in a writable view
	fwrite($exportableXML, $strxml); //write the converted xml format to 
	fclose($exportableXML);
	

	//compiling the end of the the file to complete the xml format including the closing of ">"
	ftruncate(fopen($CreatedXMLFile, 'r+'), filesize($CreatedXMLFile) - strlen(PHP_EOL));
	$append = fopen($CreatedXMLFile, 'a');
	fwrite($append, '>');
	fclose($append);
	
}

//run function for each CSV file to create the normilise
for($i = 0, $size = count($station_list); $i < $size; ++$i) {

    Normilise("$station_list[$i].csv", "$station_list[$i].xml");
}

//removes 'Warning: Undefined array' from the php output
ob_clean();

//outputs the exacution time of the script
echo '<p>Time Taken = '; echo microtime(true) - $startTime; echo ' seconds.';