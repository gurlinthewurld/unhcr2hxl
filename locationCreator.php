<!DOCTYPE html>
<html lang="en">
<head>
	<title>UNHCR2HXL Location creator</title>
    
</head>
<body>
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
    
    $stringData = makeContainerHeader($reportDate, $ttlContainerUri, null, null, $ttlContainerHeaderLocations, null, null);
    
    echo "Number of locations to lookup: " . $dbContent->num_rows;
    
    $addedLocationCounter = 0;
    
    while($row = mysqli_fetch_array($dbContent))
    {
        if (empty($row['aplpcode']) &&
            empty($row['pplpcode']))
        {
            $ttl = '';
            
            /*if (empty($row['pplpcode'])) // Add a populated place
            {*/
                $addedLocationCounter++;
                $log->write("PP missing for " . $row['name']);
            
                $ttlSubject = "<http://hxl.humanitarianresponse.info/data/locations/apl/" . $row['countrycode'] . "/" . $row['easyname'] . ">";
                $ttl .= $ttlSubject . " a hxl:PopulatedPlace . "; 
                $ttl .= $ttlSubject . " hxl:pcode \"" . $row['easyname'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:featureName \"" . $row['name'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:featureRefName \"" . $row['name'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:atLocation <http://hxl.humanitarianresponse.info/data/locations/admin/" . $row['countrycode'] . "/" . $row['defaultlocationpcode'] . "> . "; 
                
                $stringData .= $ttl;   
            }
            else
            {
                continue;
            }
       /* }
        else // add atLocation to the APL
        {
            /*if (empty($row['defaultlocationpcode'])) // Add a populated place
            {*
                $ttlSubject = "<http://hxl.humanitarianresponse.info/data/locations/apl/" . $row['countrycode'] . "/" . $row['aplpcode'] . ">";
                $ttl .= $ttlSubject . " hxl:atLocation <http://hxl.humanitarianresponse.info/data/locations/admin/" . $row['countrycode'] . "/" . $row['defaultlocationpcode'] . "> . "; 
            }
            /*else // groupe of APLs located in a PPL
            {
                $ttlSubject = "<http://hxl.humanitarianresponse.info/data/locations/apl/" . $row['countrycode'] . "/" . $row['easyname'] . ">";
                $ttl .= $ttlSubject . " a hxl:APL . "; 
                $ttl .= $ttlSubject . " hxl:pcode \"" . $row['easyname'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:featureName \"" . $row['name'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:featureRefName \"" . $row['name'] . "\" . "; 
                $ttl .= $ttlSubject . " hxl:atLocation <http://hxl.humanitarianresponse.info/data/locations/admin/" . $row['countrycode'] . "/" . $row['defaultlocationpcode'] . "> . "; 
            }*/

            $stringData .= $ttl;   
        //}
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
     
    
    echo "<br /><br />";
    echo htmlspecialchars($query);
    echo "<br /><br />";
    if ($errorCount == 1) 
    {
        echo "error";
    }
    else
    {
        echo "success";
    }
        
    echo "<br>";
    echo 'Number of fake locations added: ' . $addedLocationCounter;
}

$log->write("");
$log->close(); 

?>

</body>
</html>