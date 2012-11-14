<form action="" method="get">
Delete a container: <input type="text" name="container" value="">
<input type="submit" value="Submit">
</form>

<?php

include('inc/init.php');

$log = new Logging();
$log->file($logDelete);

if (!empty($_GET)) 
{
    $query = ' DROP GRAPH <' . htmlspecialchars($_GET['container']) . '>'; 
    echo htmlspecialchars($query);
    sparqlUpdate($query);
}

?>
