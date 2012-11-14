<form action="" method="get">
Delete containers from an emergency: <input type="text" name="emergency" value="">
<input type="submit" value="Submit">
</form>
Warning, it will delete all containers which header specifies <container-URI> hxl:aboutEmergency <your-input>.
Have a try before deleting:
<?php echo '<a href="' . htmlspecialchars('http://sparql.carsten.io/?query=PREFIX%20hxl%3A%20%3Chttp%3A//hxl.humanitarianresponse.info/ns/%23%3E%0A%0ASELECT%20%20*%20WHERE%20{%0A%20%20%3Fa%20hxl%3AaboutEmergency%20%3Chttp%3A//hxl.humanitarianresponse.info/data/emergencies/mali2012test%3E%20.%0A}&endpoint=http%3A//hxl.humanitarianresponse.info/sparql') . '" target="_blank" >The list of containers related to the Mali 2012 test emergency</a><br /><br />'; ?>
        
<?php

include('inc/init.php');

$log = new Logging();
$log->file($logDelete);

if (!empty($_GET)) 
{
    $query= "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX hxl: <http://hxl.humanitarianresponse.info/ns/#>

    SELECT ?graph WHERE {
    ?graph hxl:aboutEmergency <" . $_GET['emergency'] . "> .
    }";

    $queryResult = getQueryResults($query);

    if ($queryResult->num_rows() == 0) echo 'no result';
    else 
    {
        $return = '';
        // To extract coordinates from the polygon string.
        while( $row = $queryResult->fetch_array() )
        {  
            $query = ' DROP GRAPH <' . htmlspecialchars($row["graph"]) . '>'; 
            echo htmlspecialchars($query);
            sparqlUpdate($query);
        } 
    }
}

?>
