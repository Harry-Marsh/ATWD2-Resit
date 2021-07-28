<?php
//store starting time of process
$startTime = microtime(TRUE);

//Function for Conversion
function Normilise($SourceCSVFile, $GeneratedXMLFile) {
	
	//Open the source file.
	$inputtedCSVFile  = fopen($SourceCSVFile, 'rt');
		
	//setting the encoding of the xml file and creating a new document.
	$XMLDoc  = new DomDocument('1.0',"UTF-8");
	$XMLDoc->formatOutput = true;
	
	//getting the second line of the csv file
	$FileRow = fgets($inputtedCSVFile);
	
    //exploding the row stored to identifiy individual elements as columns
	[$siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long] = explode(',', $FileRow);

    //replaceing the FileRow from a string to an array of elements.
	$FileRow = array($siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long);
	
	//creating the root element for the station.
	$rootElement = $XMLDoc->createElement('station');
	

    //checks for empty values in the siteID column
	if ($FileRow[0] != null){
       $rootAttribute = $XMLDoc -> createAttribute('id'); //create new atribite of 'id'
        $rootAttribute -> value = $FileRow[0]; //set atributes value to the value of the siteID
		$rootElement -> appendChild($rootAttribute);
	}
	


///////////////////////////////////////

	//Creating the element of name for the station record
	if ($FileRow[14] != null){
		$rootAttribute = $XMLDoc -> createAttribute('name');
		
			
		// Add extra measures
		// to catch any special
		// characters in
		// data.
		$ampersand_catch = $FileRow[14];
		$updated_name = str_replace('&', '&amp;', $ampersand_catch);
		$character_catch = $updated_name;
		$name = str_replace('"', '', $character_catch);
		$rootAttribute -> value = $name;
		$rootElement -> appendChild($rootAttribute);
	}
	




	if ($FileRow[15] != null){
		$rootAttribute = $XMLDoc -> createAttribute('geocode');
		// Special character
		// check for blank
		// lines in names.
		$character_catch = $FileRow[15].','.$FileRow[16];
		$geocode = substr($character_catch,0,-1);
		$rootAttribute -> value = $geocode;
		$rootElement -> appendChild($rootAttribute);
	}
	
	// Finish root element
	// and append root to document.
	$XMLDoc -> appendChild($rootElement);
	
	while (($FileRow = fgets($inputtedCSVFile)) !== FALSE)
	{
	
		// Same principle as
		// root element, get
		// line and split
		// data and store to 
		// array.
		list($siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long) = explode(',', $FileRow);
	
		$FileRow = array($siteID,$ts,$nox,$no2,$no,$PM10,$NVPM10,$VPM10,$NVPM2_5,$PM2_5,$VPM2_5,$CO,$O3,$SO2,$location,$lat,$long);
		
		//checks if there is data in the 3rd column before continuing.
		if ($FileRow[2] != null) {
            //create new child element of stations
			$childElement = $XMLDoc -> createElement('rec');
		
				$childElementAtt = $XMLDoc -> createAttribute('ts');
				$childElementAtt -> value = $FileRow[1];
				$childElement -> appendChild($childElementAtt);
			
				$childElementAtt = $XMLDoc -> createAttribute('nox');
				$childElementAtt -> value = $FileRow[2];
				$childElement -> appendChild($childElementAtt);
			
			if ($FileRow[3] != null){
				$childElementAtt = $XMLDoc -> createAttribute('no');
				$childElementAtt -> value = $FileRow[3];
				$childElement -> appendChild($childElementAtt);
			}
			
			if ($FileRow[4] != null){
				$childElementAtt = $XMLDoc -> createAttribute('no2');
				$childElementAtt -> value = $FileRow[4];
				$childElement -> appendChild($childElementAtt);
			}

			
		
		}
		// append remaining child
		// elements to root element.
		$rootElement -> appendChild($childElement);
		
	}
	
	// Assign variable
	// to store document
	// xml format.
	$strxml = $XMLDoc->saveXML();
	
	// Write 'doc' to 
	// output file 
	// and close.
	$output = fopen($GeneratedXMLFile, "w");
	fwrite($output, $strxml);
	fclose($output);
	

	// Saving the document
	// as is produces an 
	// extra line at end
	// of document.
	// Code below removes 
	// the last character
	// and re-adds the
	// closing bracket
	// to complete 
	// output document
	// and meet line
	// count.
	ftruncate(fopen($GeneratedXMLFile, 'r+'), filesize($GeneratedXMLFile) - strlen(PHP_EOL));
	$append = fopen($GeneratedXMLFile, 'a');
	fwrite($append, '>');
	fclose($append);
	
}



//////////////////////////////////////////////////////////////////////////



//run function for each CSV file to create the normilised xml

Normilise("data_188.csv", "data_188.xml");
Normilise("data_203.csv", "data_203.xml");
Normilise("data_206.csv", "data_206.xml");
Normilise("data_209.csv", "data_209.xml");
Normilise("data_213.csv", "data_213.xml");
Normilise("data_215.csv", "data_215.xml");
Normilise("data_228.csv", "data_228.xml");
Normilise("data_270.csv", "data_270.xml");
Normilise("data_271.csv", "data_271.xml");
Normilise("data_375.csv", "data_375.xml");
Normilise("data_395.csv", "data_395.xml");
Normilise("data_447.csv", "data_447.xml");
Normilise("data_452.csv", "data_452.xml");
Normilise("data_459.csv", "data_459.xml");
Normilise("data_463.csv", "data_463.xml");
Normilise("data_481.csv", "data_481.xml");
Normilise("data_500.csv", "data_500.xml");
Normilise("data_501.csv", "data_501.xml");

//removes 'Warning: Undefined array' from the php output
ob_clean();

//outputs the exacution time of the script
echo '<p>Time Taken = '; echo microtime(true) - $startTime; echo ' seconds.';