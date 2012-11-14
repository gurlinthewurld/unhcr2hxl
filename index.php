
<?php
// PHP 5.2.9 minimum

include_once('inc/init.php');

$log = new Logging();
$log->file($logETL);

?>
<h1>ETL for the 2012 UNHCR Mali Emergency data dump</h1>
<h2>Description</h2>
The script extracts the values to build the turtle rdf description following the HXL standard.<br />
For doing so, it is necessary to make sure that the locations. If it is not, a log is written and it is possible to use the location creator that helps creating a real or fake link to the real or temporary fake location.<br />
Importance is also given to the source of the information. It is checked if the sources are known. If they are not a log is written and another script is able to translate the concatenated expressions to a liste of sources, know or not in order to be able to keep all this information in the generated container.<br />
<br />
The translation tables can then refer to real or fake values. Fake values are used by default, but they can be replaced by the correct pcodes or official abbreviations for linking this new data to already existing information.<br />
<br />
Scripts help to remove a container or all the containers about an emergency, Please, note that you can check first what you want to delete before validating the form.<br />


<h2>ETL</h2>
<a href="creatorLocation.php" >Location creator</a>. Creates locations according the translation table if they don't already exist. Update the settlementpcode table with known pcodes to allow recognition of known locations.<br />
<br />
<a href="etl.php" >Complete ETL</a>. Analyses the data and then proposes several steps to store the data.<br />
<br />
<h2>Delete</h2>
<a href="deleteAContainer.php" >Delete a container</a>.<br />
<br />
<a href="deleteAllContainersFromEmergency.php" >Delete ALL containers from an emergency</a>.<br />
<br />
<h2>Misc</h2>
<a href="printPcodeTable.php" >Display the settlement pcode table</a>.<br />
<br />
<a href="edit" >Edit the pcodes of the pcode table</a>.<br />
<br />
<br />
<br />
<br />
<br />

<?php
/*
echo "<h1>UNHCR db to HXL</h1>";
echo "<h2>Print</h2>";
echo "<a href=\"printPcodeTable.php\" >Display the settlement pcode table</a>.<br /><br />";
//echo "<a href=\"printAll.php\" >Print containers and their URIs</a>.<br /><br />";
echo "<h2>Load</h2>";
echo "<a href=\"creatorLocation.php\" >Location creator</a>.<br /><br />";
echo "<a href=\"etl.php\" >Complete ETL</a>.<br /><br />";
//echo "<a href=\"insertSeveralContainers.php\" >Insert several data containers</a>.<br /><br />";
//echo "<a href=\"insertFirstContainer.php\" >Insert the first container to the triple store</a>.<br /><br />";
echo "<h2>Delete</h2>";
echo "<a href=\"deleteAContainer.php\" >Delete a container</a>.<br /><br />";
echo "<a href=\"deleteAllContainersFromEmergency.php\" >Delete ALL containers from an emergency</a>.<br /><br />";
echo "<h2>Edit</h2>";
echo "<a href=\"edit\" >Edit the pcodes of the pcode table</a>.<br /><br />";
echo "<br />";
echo "<br />";
echo "<br />";
*/
if (!function_exists('curl_init'))
{
    $log->write("CURL is not installed!");
    $log->close();  
    die('CURL is not installed!');
}

?>