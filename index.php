
<?php
// PHP 5.2.9 minimum

include_once('inc/init.php');

$log = new Logging();
$log->file($logETL);

?>
<h1>ETL for the 2012 UNHCR Mali Emergency data dump</h1>
<h2>Description</h2>
The ETL works with the data dump as a source of data but also uses a translation table to convert the location into Affected People Location (hxl:APL) or Populated places (hxl:PopulatedPlace).<br />
The first step lies in the creation of missing location so that the population count can be located.<br />
Then the ETL will create te description of populations and connect it to places.<br />
<br />
Scripts help to remove a container or all the containers about an emergency, Please, note that you can check first what you want to delete before validating the form.<br />


<h2>ETL</h2>
<a href="locationCreator.php" >Location creator</a>. Creates locations according the translation table if they don't already exist. Update the settlement pcodes table with known pcodes manually first.<br />
<br />
<a href="etl.php" >Complete ETL</a>. Analyses the data and then proposes several steps to store the data.<br />
<br />
<h2>Delete</h2>
<a href="deleteAContainer.php" >Delete a container</a>.<br />
<br />
<a href="deleteAllContainersFromEmergency.php" >Delete ALL containers about an emergency</a>.<br />
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
if (!function_exists('curl_init'))
{
    $log->write("CURL is not installed!");
    $log->close();  
    die('CURL is not installed!');
}

?>