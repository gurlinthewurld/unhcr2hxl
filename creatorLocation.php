<?php

include('inc/init.php');

$log = new Logging();
$log->file($logCreator);


// Query
$mysqli = new mysqli($sourceConfig['db_host'], $sourceConfig['db_user_name'], $sourceConfig['db_password'], $sourceConfig['db_name']);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

    $errorCount = 0;
    $successCount = 0;
    $successfulLookup = 0;
    
    /*
    echo <br>';
    echo htmlspecialchars($mysqlQueryPcodes);
    echo '<br>';
     * 
     */
    
// Checking the settlements pcode tables
if ($dbContent = $mysqli->query($mysqlQueryPcodes))
{
    $stringData = '';
    $ttlContainerUri = str_replace("[%timeStamp%]", $timeStampLocationContainer, $ttlContainerUri);
    $dateTime = new DateTime();
    $reportDate = $dateTime->format('Y-m-d H:i:s');
    
    $stringData = makeContainerHeader($reportDate, $ttlContainerUri, null, $ttlContainerHeaderLocations, null, null);
    
    echo "Number of locations to lookup: " . $dbContent->num_rows;
    
    $addedLocationCounter = 0;
    
    while($row = mysqli_fetch_array($dbContent))
    {
        $pcodeOk = false;
        $countryCodeOk = false;
        $countryCode = '';
        //echo print_r($row);
        //echo '<br>';
        if (empty($row['countryPcode']))
        {
            $log->write("Country pcode missing for " . $row['settlementName']);
            //$loadIt = false;
            
            continue;
        }
        else
        {
            $countryCode = $row['countryPcode'];
        }
        if (empty($row['settlementPcode']))
        {
            $log->write("Settlement pcode missing for " . $row['settlementName']);
            //$loadIt = false;
            
            continue;
        }
        else
        {
            if (!pcodeLookup($row))// && !nameLookup($row))
            {
                $addedLocationCounter++;
                
                //$log->write("Look up failed. Description is about to be loaded for " . $row['settlementPcode']);
                
                $aplUri = "http://hxl.humanitarianresponse.info/data/locations/apl/" . $countryCode . "/" . $row['settlementPcode'];
                $ttl = "<" . $aplUri . "> " . "a <http://hxl.humanitarianresponse.info/ns/#APL> . ";
                $ttl .= "<" . $aplUri . "> hxl:pcode \"" . $row['settlementPcode'] . "\" . ";
                //$ttl .= "<" . $aplUri . "> " . "hxl:featureName \"" . $row['settlementName'] . "\" . ";
                $ttl .= "<" . $aplUri . "> " . "hxl:featureName \"" . $row['settlementPcode'] . "\" . ";
                $ttl .= "<" . $aplUri . "> " . "<http://hxl.humanitarianresponse.info/ns/#atLocation> <http://hxl.humanitarianresponse.info/data/locations/admin/" . $countryCode . "/" . $countryCode . "> . ";
                $ttl .= "<" . $aplUri . "> " . "geo:hasGeometry <" . $aplUri . "/geom> . ";
                $ttl .= "<" . $aplUri . "/geom> " . "a geo:Geometry . ";
                $ttl .= "<" . $aplUri . "/geom> " . "geo:hasSerialization \"POINT (-0.717778 14.5981)\" . ";
                
                $stringData .= $ttl;             
            }
            else
            {
                $successfulLookup++;
            }
        }
    }
        
    $query = htmlspecialchars_decode($ttlPrefixes) . ' INSERT DATA { GRAPH <' . $ttlContainerUri . '> { ' . $stringData . '}}';
               
    if ($addedLocationCounter > 0)
    {
        if (!sparqlUpdate($query))
        {
            $errorCount++;
        }
        else
        {
            $successCount++;
        }
    }
          
    //echo "<br /><br />";
    //echo htmlspecialchars($query);
    echo "<br /><br />";
    echo "errorCount: ";
    echo $errorCount;
    echo "<br>";
    echo "successCount: ";
    echo $successCount;
    echo "<br>";
    echo "<br>";
    echo "successfulLookups (pcode match means no fake tth location added to the triple store): ";
    echo $successfulLookup;
    echo '<br>';
    echo 'Number of fake location added: ' . $addedLocationCounter;
}
 
$log->close(); 

?>
