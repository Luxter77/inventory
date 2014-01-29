<?php
$enable_location_select=false;

function get_location($dbconn,$id,$with_links=TRUE) {
	$string="";
	$result=pg_query($dbconn,"SELECT location,location_description FROM objects WHERE id='$id';");
	if ($row=pg_fetch_assoc($result)) {
		$id=$row['location'];
		if($row['location_description'] != '') {
			$string="(${row['location_description']})";
		}
	} else {
		return "Object location not found!";
	}

	do {
		$result=pg_query($dbconn,"SELECT id,location,object_name,models.type, location_description FROM objects inner join models on models.model=objects.model WHERE id='$id';");
		if ($row=pg_fetch_assoc($result)) {
			if ($string!="") {
				$string="&raquo;".$string;
			}

			if ($with_links) {
				$string="</a>".$string;
			}
			$object_name=$row['object_name'];
			if($object_name == '') {
				$object_name = $row['type'].' '.$row['id'];
			}
			$string=$object_name.$string;
			if ($with_links) {
				$string="<a href=\"object.php?object=$id\">".$string;
			}
			if($row['location_description'] != '') {
				$string="(".$row['location_description'].")&raquo;".$string;
			}
			$id=$row['location'];
		} else {
			break;
		}
	} while ($id!=NULL);
	return $string;
	}

function navigation_bar() {
	echo "<DIV id=\"navigation\">\n";
	echo "<a class=\"navbutton\" href=\"models.php\">Models list</a>\n";
	echo "<a class=\"navbutton\" href=\"objects.php\">Objects list</a>\n";
	echo "<a class=\"navbutton\" href=\"users.php\">User list</a>\n";
	echo "<a class=\"navbutton\" href=\"owners.php\">Owner list</a>\n";
	echo "<a class=\"navbutton\" href=\"orders.php\">Order list</a>\n";
	echo "<form action=\"object.php\" method=\"get\">";
	echo "<div id=navsection>";
	echo "goto Object ";
	echo "<input type=\"text\" size=\"5\" name=\"object\"/>";
	echo "</div>\n";
	echo "</form>";
	echo "</DIV>\n";
}

function extra_header_content() {
	global $enable_location_select;
	if( $enable_location_select == TRUE ) {
	echo <<<EOT
		<script type="text/javascript">
		var xmlhttp;
		function reLoad() {
			var select = document.getElementById("initial_location_selector");
			select.form.reset();
		}
		window.onload = reLoad;
		function nextSelectLocationBox(caller,id)
		{
			var option=caller.value;
			var parent_span =caller.parentNode;
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.open("GET","location_serv.php?location="+option+"&id="+id,false);
			xmlhttp.send(null);
			if(xmlhttp.responseText != "") {
				parent_span.innerHTML=xmlhttp.responseText;
			}
		}
		</script>
EOT;
	}
}

function select_location($id="",$location="") {
	echo "<span id=\"selectLocation_container\" class=\"select_location\">";
	include "http://localhost/".dirname($_SERVER['PHP_SELF'])."/location_serv.php?location=$location&id=$id";
	echo "</span>\n";
}

function select_user($dbconn,$olduser="",$inputname="userid") {
	$result = pg_query($dbconn, "SELECT userid,name FROM users ORDER BY split_part(name,' ',2);");
	
	echo "<SELECT name=\"$inputname\">\n";
	if ($olduser=="") {
		$sel="selected";
	} else {
		$sel="";
	}
	echo "<OPTION $sel value='NULL'>no one</OPTION>\n";

	while ($row=pg_fetch_assoc($result)) {
		if ($olduser==$row['name']) {
			$sel="selected";
		} else {
			$sel="";
		}
		echo "<OPTION $sel value={$row['userid']}>{$row['name']}</OPTION>\n";
	}
	echo "</SELECT>\n";
}

function select_owner($dbconn,$oldowner="") {
	$result = pg_query($dbconn, "SELECT ownerid,owner_name FROM owners;");
	
	echo "<SELECT name=\"ownerid\">\n";
	if ($oldowner=="") {
		$sel="selected";
	} else {
		$sel="";
	}
	echo "<OPTION $sel value='NULL'>no one</OPTION>\n";

	while ($row=pg_fetch_assoc($result)) {
		if ($oldowner==$row['ownerid']) {
			$sel="selected";
		} else {
			$sel="";
		}
		echo "<OPTION $sel value={$row['ownerid']}>{$row['owner_name']}</OPTION>\n";
	}
	echo "</SELECT>\n";
}




?>
