<?php
/**************************************************************************************
 * update row in database - update.php
 * Author: Shane  - <shane *at* hackosis *dot* com>
 * use this to learn, share, or what ever else floats your boat and finds your lost remote
 **************************************************************************************/

//Get database credentials
require 'config.php';

$column1 = $_GET['column1'];
$column2 = $_GET['column2'];
$column3 = $_GET['column3'];
$column4 = $_GET['column4'];
$column5 = $_GET['column5'];

// connect to the mysql database server.
mysql_connect ($dbhost, $dbusername, $dbuserpass);
//select the database
mysql_select_db($dbname) or die('Cannot select database');

$query = "UPDATE table1" .
" SET column2 = '".$column2."'," .
" column3 = '".$column3."'," .
" column4 = '".$column4."'," .
" column5 = '".$column5."'" .
" WHERE column1 = '".$column1."'";

//Run the query
$result = mysql_query($query) or die(mysql_error());

//link variable is equal to the referring page
$link = $_SERVER['HTTP_REFERER'];
//sends a header directly to the browser telling it to redirect the user to the referring page
header("Location: $link");

?>