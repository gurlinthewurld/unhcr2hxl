<?php

/*****************************
 * Puts the values into the ttl query
 *****************************/
function extractRowData($row)
{
    global $reporter, $ttlContainerHeader, $ttlContainerUri, $ttlPrefixes, $currentEmergency;
    global $log;
    //global $timeStamp, $scriptDate;

    $containerUriArray = array();
    $containerArray = array();
    

    $dateTime = new DateTime();
    $scriptDate = $dateTime->format('Y-m-d H:i:s');
    $timeStamp = microtime(true);

    $stringData = '';

    $tempContainerUri = str_replace("[%timeStamp%]", $timeStamp, $ttlContainerUri);
    array_push($containerUriArray, $tempContainerUri);
    
    //$stringData .= $ttlPrefixes;
    $reportDate = $row['ReportDate'];
    if ($row['ReportDate'] != $row['UpdatedDate'])
    {
        $reportDate = $row['UpdatedDate'];
    }
    $stringData .= makeContainerHeader($scriptDate, $tempContainerUri, $reporter, $ttlContainerHeader, $currentEmergency, $reportDate);
    $stringData .= makeTtlFromRow($row);
    
    array_push($containerArray, $stringData);	


    
    
    global $storeConfig, $curlDropOneContainer;
    
    //echo $curlDropOneContainer;
    //echo "-------------------";
    
    
    
    $tempDrop = str_replace("[%graph%]", $tempContainerUri, $curlDropOneContainer);
    $tempDrop = str_replace("[%userPass%]", $storeConfig['store_username'] . ':' . $storeConfig['store_password'], $tempDrop);
    $tempDrop = str_replace("[%endPoint%]", $storeConfig['store_endpoint'], $tempDrop);
    
    
    //echo $tempDrop;
    //echo "-------------------";
    
    
    
    
    return array ($containerUriArray, $containerArray, $tempDrop);
}

/*****************************
 * TODO: if necessary, print each line converting HTML characters into printable ones.
 *****************************/
function printContainers($dbContent)
{
    global $reporter, $ttlContainerHeader, $ttlContainerUri, $ttlPrefixes, $currentEmergency;
    global $timeStamp, $scriptDate;

    global $log;

    $log->write("----------------------------------------------------------------");
    $log->write("Number of rows: " . $dbContent->num_rows);
    $log->write("Number of count columns: 11");
    $log->write("General note: The default population type is hxl:RefugeesAsylumSeekers");
    $log->write("General warning: Unknow reporter IDs and absent or multiple sources in table datarefpop. The hxl:reportedBy is set to $reporter.");
    $log->write("General warning: No country pcode. Using temporary pcodes from the conversion table.");
    $log->write("General warning: No APL pcode. Using temporary pcodes from the conversion table.");
    $log->write("General warning: No origin pcode. Using temporary pcodes from the conversion table.");
    $log->write("General warning: No method reported.");

    $i = 0;
    $containerUriArray = array();
    $containerArray = array();
    while($row = mysqli_fetch_array($dbContent))
    {
        $stringData = '';
        $i++;

        $ttlContainerUri = str_replace("[%timeStamp%]", $timeStamp, $ttlContainerUri);
        array_push($containerUriArray, $ttlContainerUri);

        $stringData .= $ttlPrefixes;
        
        $reportDate = $row['ReportDate'];
        if ($row['ReportDate'] != $row['UpdatedDate'])
        {
            $reportDate = $row['UpdatedDate'];
        }
        $stringData .= makeContainerHeader($scriptDate, $ttlContainerUri, $reporter, $ttlContainerHeader, $currentEmergency, $reportDate);
        
        $ttfRow = makeTtlFromRow($row);
        if ($ttfRow != false)
        {
            $stringData .= $ttfRow;
        }
        
        array_push($containerArray, $stringData);


        if($i == 2) break;
        

    /* If need to write in a file, reuse this code
    $fileHandle = fopen($ourFileName, 'w') or die("can't open file");
    fwrite($fileHandle, $stringData);
    fclose($fileHandle);
 */

			
     }	
     /* to print the list of container URIs and containers */
    print_r("<pre>");
    print_r($containerUriArray);
    print_r("</pre>");
    print_r("<br />");
    print_r("<pre>");
    print_r($containerArray);
    print_r("</pre>");
     
}

/*******************************
 *
 *****************************/
function makeContainerHeader($scriptDate, $containerUri,  $reporter, $ttlContainerHeader, $currentEmergency, $reportDate)
{
    $ttlContainerHeader = str_replace("[%containerUri%]", $containerUri, $ttlContainerHeader);
    if (!is_null($currentEmergency)) $ttlContainerHeader = str_replace("[%currentEmergency%]", $currentEmergency, $ttlContainerHeader);

    $ttlContainerHeader = str_replace("[%reportDate%]", $scriptDate, $ttlContainerHeader);
    if (!is_null($reporter)) $ttlContainerHeader = str_replace("[%reporter%]", $reporter, $ttlContainerHeader);

    //$tempTtlContainerHeader = $ttlContainerHeader;
    if (!is_null($reportDate)) $ttlContainerHeader = str_replace("[%validOn%]", $reportDate, $ttlContainerHeader);

    return $ttlContainerHeader;
}

/*******************************
 *
 *****************************/
function isRowOk($dbRow)
{
    global $log;
    
    $lookUpSuccessful = false;
    
    $query= "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX hxl: <http://hxl.humanitarianresponse.info/ns/#>

    SELECT * WHERE {
    ?location hxl:pcode \"" . $dbRow['settlementPcode'] . "\" .
    ?location a ?type .
    }";
/*
    echo '<br>';
    echo htmlspecialchars($query);
    echo '<br>';
*/ 
    
    /*
    if ($dbRow['settlementPcode'] == "UNHCR-POC-81-Test" ||
        $dbRow['settlementPcode'] == "UNHCR-POC-80")
                $log->write($dbRow['settlementPcode'] . " iiiiiiiiiiiiis: ");
*/
    $queryResult = getQueryResults($query);
    if ($queryResult->num_rows() == 0) 
    {
        $log->write("Error: pcode " . $dbRow['settlementPcode'] . " not found.");
    }
    else 
    {
        //print_r($queryResult);
        while($row = $queryResult->fetch_array())
        {  
            if ($row["type"] == 'http://hxl.humanitarianresponse.info/ns/#APL')
            {
                $lookUpSuccessful = true;
                //$log->write($dbRow['settlementPcode'] . " found!!!");
            }
            else
            {
                $log->write($dbRow['settlementPcode'] . " is of type: " . $row["type"] . ".");
            }
        } 
    }
     
    if(countAvailable($dbRow, $dbRow['ReportDate']) &&
       $lookUpSuccessful)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*******************************
 * Check the existence of a pcode
 * returns true if the pcode correspond to an APL
 * returns false otherwise
 * logs an error message containing the type if it exists but is not an APL
 *****************************/
function pcodeLookup($dbRow)
{
    global $log;
    
    $lookUpSuccessful = false;
    
    $query= "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX hxl: <http://hxl.humanitarianresponse.info/ns/#>

    SELECT * WHERE {
    ?location hxl:pcode \"" . $dbRow['settlementPcode'] . "\" .
    ?location a ?type .
    }";
 
/*
    echo '<br>';
    echo htmlspecialchars($query);
    echo '<br>';
 */
    
    $queryResult = getQueryResults($query);
    
    if ($queryResult->num_rows() == 0) 
    {
        $log->write("Look up failed. Description is about to be loaded for " . $row['settlementPcode']);
    }
    else 
    {
        //print_r($queryResult);
        while($row = $queryResult->fetch_array())
        {  
            if ($row["type"] == 'http://hxl.humanitarianresponse.info/ns/#APL')
            {
                $lookUpSuccessful = true;
                //$log->write($dbRow['settlementPcode'] . " found!!!");
            }
            else
            {
                $log->write($dbRow['settlementPcode'] . " is of type: " . $row["type"] . ".");
            }
        } 
    }
     
    return $lookUpSuccessful;
}

/*******************************
 * Check the existence of a location name
 * returns true if the pcode correspond to an APL
 * returns false otherwise
 * logs an error message containing the type if it exists but is not an APL
 *****************************/
function nameLookup($dbRow)
{
    global $log;
    
    $lookUpSuccessful = false;
    
    $query = "PREFIX hxl: <http://hxl.humanitarianresponse.info/ns/#>

    SELECT * WHERE {
    ?location hxl:featurefName \"" . trim($dbRow['settlementName']) . "\" . 
    ?location a ?type .
    }";

    
/*
    echo '<br>';
    echo htmlspecialchars($query);
    echo '<br>';
*/ 
    
    /*
    if ($dbRow['settlementPcode'] == "UNHCR-POC-81-Test" ||
        $dbRow['settlementPcode'] == "UNHCR-POC-80")
                $log->write($dbRow['settlementPcode'] . " iiiiiiiiiiiiis: ");
*/
    $queryResult = getQueryResults($query);
    
    
echo $queryResult->num_rows();
    echo '<br>';
    
    
    if ($queryResult->num_rows() == 0) 
    {
        $log->write("Error: name " . trim($dbRow['settlementName']) . " not found.");//
    }
    else 
    {
        //print_r($queryResult);
        while($row = $queryResult->fetch_array())
        {  
            if ($row["type"] == 'http://hxl.humanitarianresponse.info/ns/#APL')
            {
                $lookUpSuccessful = true;
                //$log->write(trim($dbRow['settlementName']) . " found!!!");
            }
            else
            {
                $log->write(trim($dbRow['settlementName']) . " is of type: " . $row["type"] . ".");
            }
        } 
    }
     
    return $lookUpSuccessful;
}

/*******************************
 *
 *****************************/
function makeTtlFromRow($row)
{
    global $log, $sources;

    $stringData = '';

    $rowSources = strtolower(str_replace(" ", "", $row['source']));
    $currentSources = array();
    /*
    echo 'proof on ' . $rowSources . ' : <br>';
    print_r($sources);
    echo '<br>';
    */
    if (empty($rowSources) ||
        count($sources[$rowSources]) == 0) {
        $currentSources[$rowSources] = "UNHCR";
        $log->write("No source found for the report of " . $row['ReportDate']);
    }
    else
    {
        $currentSources = $sources[$rowSources];
    }
    
    /*
    echo '<pre>';
    print_r($sources);
    echo '</pre>';
     *
    echo 'source:<br>';
    echo $row['source'];
    echo 'source:<br>';
    echo $rowSources;
    echo 'Sources for a row: <br>';
    print_r($currentSources);
    echo '<br>';
    */
    ////////////////////
    $sex = "";
    $age = "";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['TotalRefPop_HH'], $currentSources);


    ////////////////////
    $sex = "";
    $age = "";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['TotalRefPop_I'], $currentSources);

    ////////////////////
    $age = "ages_0-4";
    //$row['DEM_04_M']
    $sex = "male";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_04_M'], $currentSources);
    //$row['DEM_04_F']
    $sex = "female";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_04_F'], $currentSources);

    ////////////////////
    $age = "ages_5-11";
    //$row['DEM_511_M']
    $sex = "male";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_511_M'], $currentSources);
    //$row['DEM_511_F']
    $sex = "female";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_511_F'], $currentSources);

    ////////////////////
    $age = "ages_12-17";
    //$row['DEM_1217_M']
    $sex = "male";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_1217_M'], $currentSources);
    //$row['DEM_1217_F']
    $sex = "female";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_1217_F'], $currentSources);

    ////////////////////
    $age = "ages_18-59";
    //$row['DEM_1859_M']
    $sex = "male";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_1859_M'], $currentSources);
    //$row['DEM_1859_F']
    $sex = "female";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_1859_F'], $currentSources);

    ////////////////////
    $age = "ages_60";
    //$row['DEM_60_M']
    $sex = "male";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_60_M'], $currentSources);
    //$row['DEM_60_F']
    $sex = "female";
    $stringData .= makeTtlPopDescription($row, $sex, $age, $row['DEM_60_F'], $currentSources);

    return $stringData;

}

/******************************
 *
 *****************************/
function makeTtlPopDescription($row, $sex, $age, $popCount, $sources)
{
    global $log, $ttlPersonCount, $ttlHouseholdCount, $ttlPopDescription, $ttlSex, $ttlAge, $ttlSubject, $ttlMethod, $ttlSource;
    global $defaultPopulationType;

    $ttlSubject = str_replace("[%populationType%]", $defaultPopulationType, $ttlSubject);
    $ttlSubject = str_replace("[%countryPCode%]", $row['currentCountryPcode'], $ttlSubject);
    $ttlSubject = str_replace("[%campPCode%]", $row['settlementPcode'], $ttlSubject);
    $ttlSubject = str_replace("[%originPCode%]", $row['origin'], $ttlSubject);

    $ttlSexTemp = $ttlSex;
    $ttlAgeTemp = $ttlAge;
    if (empty($sex) && empty($age))
    {
            $ttlSexTemp = "";
            $ttlAgeTemp = "";
    }
    else
    {
            $ttlSexTemp = str_replace("[%sex%]", $sex, $ttlSexTemp);
            $ttlAgeTemp = str_replace("[%age%]", $age, $ttlAgeTemp);		
    }

    $ttlPopDescriptionTemp = $ttlPopDescription;
    $ttlPopDescriptionTemp = str_replace("[%ttlSex%]", $ttlSexTemp, $ttlPopDescriptionTemp);
    $ttlPopDescriptionTemp = str_replace("[%ttlAge%]", $ttlAgeTemp, $ttlPopDescriptionTemp);

    $ttlSubjectTemp = $ttlSubject;
    $ttlSubjectTemp = str_replace("[%originPCode%]", $row['origin'], $ttlSubjectTemp);

    //$ttlSubjectTemp = $ttlSubject;
    $ttlSubjectTemp = str_replace("[%originPCode%]", $row['origin'], $ttlSubjectTemp);
    if (empty($sex) && empty($age))
    {
        $ttlSubjectTemp = str_replace("[%sex%]/[%age%]", "household", $ttlSubjectTemp);
        $ttlPersonCountTemp = "";
        $ttlHouseholdCountTemp = $ttlHouseholdCount;
        $ttlHouseholdCountTemp = str_replace("[%householdCount%]", $popCount, $ttlHouseholdCountTemp);
        $ttlPopDescriptionTemp = str_replace("[%ttlPopCount%]", $ttlPersonCountTemp, $ttlPopDescriptionTemp);
        $ttlPopDescriptionTemp = str_replace("[%ttlHouseholdCount%]", $ttlHouseholdCountTemp, $ttlPopDescriptionTemp);
    }
    else
    {
        $ttlSubjectTemp = str_replace("[%sex%]", $sex, $ttlSubjectTemp);
        $ttlSubjectTemp = str_replace("[%age%]", $age, $ttlSubjectTemp);
        $ttlHouseholdCountTemp = "";
        $ttlPersonCountTemp = $ttlPersonCount;
        $ttlPersonCountTemp = str_replace("[%personCount%]", $popCount, $ttlPersonCountTemp);
        $ttlPopDescriptionTemp = str_replace("[%ttlPopCount%]", $ttlPersonCountTemp, $ttlPopDescriptionTemp);
        $ttlPopDescriptionTemp = str_replace("[%ttlHouseholdCount%]", $ttlHouseholdCountTemp, $ttlPopDescriptionTemp);
    }


    $ttlPopDescriptionTemp = str_replace("[%countryPCode%]", $row['currentCountryPcode'], $ttlPopDescriptionTemp);
    $ttlPopDescriptionTemp = str_replace("[%countryPCode%]", $row['currentCountryPcode'], $ttlPopDescriptionTemp);
    $ttlPopDescriptionTemp = str_replace("[%campPCode%]", $row['settlementPcode'], $ttlPopDescriptionTemp);
    //$ttlPopDescriptionTemp = str_replace("[%popTypePart%]", $popTypePart, $ttlPopDescriptionTemp);
    
    
    
    /*
    echo '<pre>';
    print_r($sources);
    echo '</pre>';
    echo count($sources) . '<br>';
    
    if (count($sources) == 0 ) echo "sou0!!!!!!!!!!!!!!!!!!";
    if (count($sources) > 1 ) echo "sou2";
    
    */
    $ttlSources = '';
    foreach ($sources as $source)
    {
    //echo '<br> -- makeTtlPopDescription -- :' . $source;
        $ttlSources .= str_replace("[%source%]", strtolower($source), $ttlSource);
    }
    
    
    
    $ttlMethodTemp = str_replace("[%method%]", "undefined", $ttlMethod);
    
    $ttlPopDescriptionTemp = str_replace("[%ttlSource%]", $ttlSources, $ttlPopDescriptionTemp);
    $ttlPopDescriptionTemp = str_replace("[%ttlMethod%]", $ttlMethodTemp, $ttlPopDescriptionTemp);
    $ttlPopDescriptionTemp = str_replace("[%subject%]", $ttlSubjectTemp, $ttlPopDescriptionTemp);

   // echo '<br> -- ttlPopDescriptionTemp: -- :' . htmlspecialchars($ttlPopDescriptionTemp);
        
    return $ttlPopDescriptionTemp;
}

/*******************************
 *
 *****************************/
function translateSources()
{
    global $mysqlQuerySources, $sourceConfig, $log;

    // Query
    $mysqli = new mysqli($sourceConfig['db_host'], $sourceConfig['db_user_name'], $sourceConfig['db_password'], $sourceConfig['db_name']);
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    $array = array();
    
    // Checking the content
    if ($dbContent = $mysqli->query($mysqlQuerySources))
    {
        while($row = mysqli_fetch_array($dbContent))
        {
            $sources = explode(",", $row['output']);
            $tmp = strtolower(str_replace(" ", "", $row['input']));
            $array[$tmp] = $sources;
        }
    }
    
    //print_r($array);
    
    return $array;
}


/* TODO:
 * Spot what is irrelevant like an empty total if there are some counts...
 */
/*
 *
 */
function countAvailable($row, $reportDate)
{
    global $log;

    if (empty($row['DEM_04_M']) &
        empty($row['DEM_04_F']) &
        empty($row['DEM_511_M']) &
        empty($row['DEM_511_F']) &
        empty($row['DEM_1217_M']) &
        empty($row['DEM_1217_F']) &
        empty($row['DEM_1859_M']) &
        empty($row['DEM_1859_F']) &
        empty($row['DEM_60_M']) &
        empty($row['DEM_60_F']) &
        empty($row['TotalRefPop_HH']) &
        empty($row['TotalRefPop_I']))
    {
        $log->write("Warning: No detail of population count found at " . $row['settlementPcode'] . " on " . $reportDate);
        return false;
    }
    else
    {
        return true;
    }
}

/*******************************
 *
 *****************************/
function printResultArray($dbContent)
{

    $i = 0;
    printf("Select returned %d rows.<br /><br />\n", $dbContent->num_rows);
    printf("We retain 11 columns of distinct age/sex type. It makes a total of 11 columns * %d rows or containers = %d population counts and household counts all together.<br /><br />\n", $dbContent->num_rows, $dbContent->num_rows * 11);

    // The Updated date doesn't correspond at all and can differ significantly from the date it is supposed to be valid on.
    // The report date is used because it is the closest from the reality.
    $reportDate = $row['ReportDate'];
    /*if ($row['ReportDate'] != $row['UpdatedDate'])
    {
        $reportDate = $row['UpdatedDate'];
    }*/

    echo "<table><tr>";
    echo "<td>#</td>";
    echo "<td>ReportDate</td>";
    echo "<td>Set. pcode</td>";

    echo "<td>country. pcode</td>";
    echo "<td>origin</td>";
    echo "<td>Total HH</td>";
    echo "<td>Total I</td>";
    echo "<td>0-4 M</td>";
    echo "<td>0-4 F</td>";
    echo "<td>5-11 M</td>";
    echo "<td>5-11 F</td>";
    echo "<td>12-17 M</td>";
    echo "<td>12-17 F</td>";
    echo "<td>18-59 M</td>";
    echo "<td>18-59 F</td>";
    echo "<td>60 M</td>";
    echo "<td>60 F</td>";
    echo "<td>Source</td>";
    echo "</tr>";
    while($row = mysqli_fetch_array($dbContent)) {
        $i++;
        echo "<tr>";
        echo "<td>$i</td>";
        echo "<td>{$reportDate}</td>";
        echo "<td>{$row['settlementPcode']}</td>";
        echo "<td>{$row['currentCountryPcode']}</td>";
        echo "<td>{$row['origin']}</td>";
        echo "<td>{$row['TotalRefPop_HH']}</td>";
        echo "<td>{$row['TotalRefPop_I']}</td>";
        echo "<td>{$row['DEM_04_M']}</td>";
        echo "<td>{$row['DEM_04_F']}</td>";
        echo "<td>{$row['DEM_511_M']}</td>";
        echo "<td>{$row['DEM_511_F']}</td>";
        echo "<td>{$row['DEM_1217_M']}</td>";
        echo "<td>{$row['DEM_1217_F']}</td>";
        echo "<td>{$row['DEM_1859_M']}</td>";
        echo "<td>{$row['DEM_1859_F']}</td>";
        echo "<td>{$row['DEM_60_M']}</td>";
        echo "<td>{$row['DEM_60_F']}</td>";
        echo "<td>{$row['source']}</td>";
        echo "</tr>";
    }
    echo "</table>";
     
    $dbContent->close();
}

/*******************************
 *
 *****************************/
function displayDropCurlCommand($graph)
{
    global $storeConfig, $curlDropOneContainer, $curlDropFile, $log;
    
    $stringData = str_replace("[%graph%]", $graph, $curlDropOneContainer);
    $stringData = str_replace("[%userPass%]", $storeConfig['store_username'] . ':' . $storeConfig['store_password'], $stringData);
    $stringData = str_replace("[%endPoint%]", $storeConfig['store_endpoint'], $stringData);
    
    
    print_r('<pre>');
    print_r($stringData);
    print_r('</pre>');
   
    $fileHandle = fopen($curlDropFile, 'w') or die("can't open file");
    fwrite($fileHandle, $stringData);
    fclose($fileHandle);

}

/*******************************
 *
 *****************************/
function displayDropEmergencyContainers($dbContent)
{
    global $storeConfig, $curlDropOneEmergency, $curlDropFile, $log;
    
    $stringData = '';
    
    while($row = mysqli_fetch_array($dbContent))
    {
        $tempDataData = str_replace("[%endPoint%]", $storeConfig['store_endpoint'], $curlDropOneContainer);
        $tempDataData = str_replace("[%userPass%]", $storeConfig['store_username'] . ':' . $storeConfig['store_password'], $tempDataData);
        $stringData .= $tempData;
    }
    
    print_r('<pre>');
    print_r($stringData);
    print_r('</pre>');
   
    $fileHandle = fopen($curlDropFile, 'w') or die("can't open file");
    fwrite($fileHandle, $stringData);
    fclose($fileHandle);
}

/*****************************
 *
 *****************************/
function giveFirstRow($dbContent)
{
    global $reporter, $ttlContainerHeader, $ttlContainerUri, $ttlPrefixes, $currentEmergency;
    global $timeStamp, $scriptDate;
    global $log;

    $i = 0;
    $containerUriArray = array();
    $containerArray = array();
    
    while($row = mysqli_fetch_array($dbContent))
    {
        $stringData = '';
        $ttlCamp = makeTtlFromRow($row);

        if ($ttlCamp != false)
        {
            $ttlContainerUri = str_replace("[%timeStamp%]", $timeStamp, $ttlContainerUri);
            array_push($containerUriArray, $ttlContainerUri);

            //$stringData .= $ttlPrefixes;
            $reportDate = $row['ReportDate'];
            if ($row['ReportDate'] != $row['UpdatedDate'])
            {
                $reportDate = $row['UpdatedDate'];
            }
            $stringData .= makeContainerHeader($scriptDate, $ttlContainerUri, $reporter, $ttlContainerHeader, $currentEmergency, $reportDate);

            array_push($containerArray, $stringData);
        }
    }
    
    return array ($containerUriArray, $containerArray);
}

?>