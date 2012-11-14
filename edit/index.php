<?php
/**************************************************************************************
 * Simple MySQL Table Editor index script - index.php
 * Author: Shane - shane *at* hackosis *dot* com>
 * use this to learn, share, or what ever else floats your boat and finds your lost remote
 **************************************************************************************/

//Get database credentials
require 'config.php';

mysql_connect ($dbhost, $dbusername, $dbuserpass);
mysql_select_db($dbname) or die('Cannot select database');

require 'header.php';

$query = "SELECT * FROM unhcr2hxl_settlementpcode;";
$result = mysql_query($query) or die(mysql_error());
$count = mysql_num_rows($result);

//Table header
echo "<div><table id=\"tableheader\" bgcolor=\"#4382b5\">\n";
echo "<tr>\n";
echo "<td>&nbsp;id:</td>\n";
echo "<td>&nbsp;pcode:</td>\n";
echo "<td>&nbsp;name 3:</td>\n";
echo "<tr>";
echo "</table></div>";
if ($count !== 0)
{
    while($row = mysql_fetch_array($result))
    {
        echo "<div class=\"addform\"><form method='get' action=\"update.php\">\n";
        echo "	<input type=\"text\" value=\"".$row['id']."\" name=\"column1\">\n";
        echo "	<input type=\"text\" name=\"column2\" value=\"".$row['pcode']."\"/>\n";
        echo "	<input type=\"text\" name=\"column3\" value=\"".$row['name']."\"/>\n";
        echo "	<input type=\"image\" src=\"images/update.png\" alt=\"Update Row\" class=\"update\" title=\"Update Row\">\n";
        echo "<a href=\"delete.php?column1=".$row['id']."\"><img title='Delete Row' alt=\"Delete\" class='del' src='images/delete.png'/></a></form></div>\n";
    }
	echo "</table><br />\n";
} else
{
    echo "<b><center>NO DATA</center></b>\n";
}
echo "<div>Add Row:</div>\n";
echo "<div class=\"addform\"><form method='get' action=\"add.php\">\n".
"	<input type=\"text\" name=\"column1\"/>\n".
"	<input type=\"text\" name=\"column2\"/>\n".
"	<input type=\"text\" name=\"column3\"/>\n".
"	<input type=\"image\" src=\"images/add.png\" alt=\"Add Row\" class=\"update\" title=\"Add Row\">\n".
"</form></div>";
?>
<div>
<br />
<b>Legend:</b>
<br />
<img alt="Add" src="images/add.png"> Add a row after entering the correct information.<br />
<img alt="Update" src="images/update.png"> Update a row after editing it.<br />
<img alt "Delete" src="images/delete.png"> Delete a row.<br />
</div>
</body>
</html>