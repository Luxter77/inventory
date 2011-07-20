<?php

include '../common/page_functions.php';
include 'functions.php';
include 'variables.php';

$typesel="";
if (isset($_GET['condition'])) {
	$cond="condition=".$_GET['condition'];
	$condition=" WHERE ".$_GET['condition'];
	if (strpos($condition,"type")!==FALSE) {
		$condparts=explode("'",$condition);
		$typesel=$condparts[1];
	}
 } else {
	$cond="";
	$condition="";
 }

page_head("Models","B1 inventory: Models");
$dbconn = pg_connect($dbstring);
if (!$dbconn) {
	  die('Could not connect: ' . pg_last_error());
};

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$query="INSERT INTO models (type,manufacturer,name,maintenance_interval,maintenance_instructions,sublocations,description,comment) VALUES (";
	$query.="'". pg_escape_string($dbconn,$_POST['type']) . "', ";
	$query.="'". pg_escape_string($dbconn,$_POST['manufacturer']) . "', ";
	$query.="'". pg_escape_string($dbconn,$_POST['name']) . "', ";
	if ($_POST['maintenance_interval']=="") {
		$query.="'10 years', ";
	} else {
		$query.="'". pg_escape_string($dbconn,$_POST['maintenance_interval']) . "', ";
	}
	$query.="'". pg_escape_string($dbconn,$_POST['maintenance_instructions']) . "', ";
	$query.="'". pg_escape_string($dbconn,$_POST['sublocations']) . "', ";
	$query.="'". pg_escape_string($dbconn,$_POST['description']) . "', ";
	$query.="'". pg_escape_string($dbconn,$_POST['comment']) . "');";
	$result=pg_query($dbconn,$query);
 }

echo '<div id=content><h1>Models</h1>';
if ($condition=="") {
	foreach ($model_types as $type) {
		echo "<a href=\"models.php?condition=type='$type'\">List of ${type} types</a><br>\n";
	}
 } else {
	echo "<table class=\"rundbtable\">\n";
	
	echo "<tr class=\"rundbhead\">";
	echo "<td>model</td>";
	echo "<td>type</td>";
	echo "<td>manufacturer</td>";
	echo "<td>model name</td>";
	echo "<td>maintenance_interval</td>";
	echo "<td>sublocations</td>";
	echo "<td>description</td>";
	echo "<td>comment</td>";
	echo "</tr>\n";
	
	$result = pg_query($dbconn, "SELECT * FROM models $condition ORDER BY model DESC;");
	while ($row=pg_fetch_assoc($result)) {
		echo "<tr class=\"rundbrun\">";
		echo "<td><a href=\"model.php?model={$row['model']}\">{$row['model']}</a></td>";
		echo "<td>{$row['type']}</td>";
		echo "<td>{$row['manufacturer']}</td>";
		echo "<td>{$row['name']}</td>";
		echo "<td>{$row['maintenance_interval']}</td>";
		echo "<td>{$row['sublocations']}</td>";
		echo "<td>{$row['description']}</td>";
		echo "<td>{$row['comment']}</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	
	echo "<h1>Add new model</h1>\n";
	echo "<form action=\"models.php?$cond\" method=\"post\">";
	echo "Type: <SELECT name=\"type\">\n";
	foreach ($model_types as $type) {
		if ($typesel==$type) {
			$sel="selected";
		} else {
			$sel="";
		}
		echo "<OPTION $sel>" . $type . "</OPTION>\n";
	}
	echo "</SELECT><br>\n";
	echo "manufacturer: <input type=\"text\" name=\"manufacturer\" size=\"20\" value=\"\"><br>";
	echo "model name: <input type=\"text\" name=\"name\" size=\"20\"><br>";
	echo "maintenance interval: <input type=\"text\" name=\"maintenance_interval\" size=\"20\"><br>";
	echo "maintenance instructions: <input type=\"textarea\" name=\"maintenance_instructions\" rows=\"5\" columns=\"50\"><br>";
	echo "sublocations: <input type=\"text\" name=\"sublocations\" size=\"60\"  value=\"\"><br>";
	echo "description: <input type=\"text\" name=\"description\" size=\"60\"  value=\"\"><br>";
	echo "comment: <input type=\"text\" name=\"comment\" size=\"60\"  value=\"\"><br>";
	echo '<input type="submit" value="Submit" >';
	echo "</form>";
 }
echo "</div>";

page_foot();
?>
