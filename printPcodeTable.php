<html><head><title>MySQL Table Viewer</title></head><body>
<?php

include('inc/init.php');

$db_host = $sourceConfig['db_host'];
$db_user = $sourceConfig['db_user_name'];
$db_pwd = $sourceConfig['db_password'];

$database = $sourceConfig['db_name'];
$table = 'unhcr2hxl_settlementpcode';

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");

// sending query
$result = mysql_query("SELECT * FROM {$table}");
if (!$result) {
    die("Query to show fields from table failed");
}

$fields_num = mysql_num_fields($result);

echo "<h1>Table: {$table}</h1>";
echo "<table border='1'><tr>";
// printing table headers
for($i=0; $i<$fields_num; $i++)
{
    $field = mysql_fetch_field($result);
    echo "<td>{$field->name}</td>";
}
echo "</tr>\n";
// printing table rows
while($row = mysql_fetch_row($result))
{
    echo "<tr>";
    foreach($row as $cell)
    {
        echo "<td>$cell</td>";
    }
    echo "</tr>\n";
}
mysql_free_result($result);
?>
</body></html>