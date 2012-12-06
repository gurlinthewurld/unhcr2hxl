<?php
session_start();

include('inc/init.php');

$log = new Logging();
$log->file($logETL);

$sliceSize = 100; 

$sources = '';

// Initial step: parsing and checking
if (!array_key_exists("slice", $_GET))
{
    // Query
    $mysqli = new mysqli($sourceConfig['db_host'], $sourceConfig['db_user_name'], $sourceConfig['db_password'], $sourceConfig['db_name']);
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    // Checking the content
    if ($dbContent = $mysqli->query($mysqlQueryAllRows)) //$mysqlQueryAllRows // $mysqlQueryManyRows
    {
        $dropScript = '';
        $sparqlQueryArray = array();
        
        $sources = translateSources();
        
        while($row = mysqli_fetch_array($dbContent))
        {
            $query =  '';
            
            if (isRowOk($row))
            {
                $containerInfo = extractRowData($row);
                $query = htmlspecialchars_decode($ttlPrefixes) . ' INSERT DATA { GRAPH <' . $containerInfo[0][0] . '> { ' . $containerInfo[1][0] . '}}';

                array_push($sparqlQueryArray, $query);

                $dropScript .= $containerInfo[2];
            }
            else
            {
                $log->write("Row not matching to a location or not containing interesting data.");
            }
        }

        //$rowCount = $dbContent->num_rows;
        $rowCount = count($sparqlQueryArray);
        
        echo '<br />row count: ';
        echo $rowCount;
        echo '<br />';
        
        $splitCount = ceil($rowCount / $sliceSize);
        $_SESSION['splitCount'] = $splitCount;

        // The data is sent to the session for the following steps
        $_SESSION['sparqlQueryArray'] = serialize($sparqlQueryArray);

        // Link to access the following steps
        echo "There are $splitCount chunks of database to parse.<br />";
        echo "Are you ready to proceed with the first slice?<br />";
        echo "<a href=?slice=1>Click here</a><br />";
        echo '<br />';
    }
    else
    {
        $log->write("Data base connection error: $mysqli->error");
        $log->close();  
    }
}
else // Processing the data
{
    $slice = $_GET["slice"];
    
    $sparqlQueryArray = unserialize($_SESSION['sparqlQueryArray']);
    $splitCount = $_SESSION['splitCount'];
    
    $splittedQueryArray = array_chunk($sparqlQueryArray, $sliceSize);

    if (count($splittedQueryArray) != $slice)
    {
        echo "There are $splitCount chunks of database to parse.<br />";
        echo "Are you ready to proceed with the next slice?<br />";
        echo "<a href=?slice=" . (intval($slice) + 1.0) . ">Click here for the slice number " . (intval($slice) + 1.0) . "</a><br />";
        echo '<br />';
    }

    $errorCount = 0;
    $successCount = 0;
    
    for($k = 0; $k < $sliceSize; $k++)
    {
        if (!array_key_exists($k, $splittedQueryArray[$slice - 1])) break;
        
        //echo htmlspecialchars($splittedQueryArray[$slice - 1][$k]);

        if (!sparqlUpdate($splittedQueryArray[$slice - 1][$k]))
        {
            $errorCount++;
        }
        else
        {
            $successCount++;
        }
  
    }
    echo "<br /><br />";
    echo "errorCount: ";
    echo $errorCount;
    echo "<br>";
    echo "successCount: ";
    echo $successCount;
    echo "<br>";
}

?>
