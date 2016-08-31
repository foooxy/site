<?php
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());
	
	$catId = htmlspecialchars($_POST['val']);
	$query = "	SELECT cats.id,
				params.name
				FROM categories AS cats
				LEFT JOIN params
				ON (cats.param = params.id)
				WHERE cats.cat = ".$catId;
	$res = mysql_query($query);
	while ($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}
	
	echo json_encode($rows);
?>