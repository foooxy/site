<?php
	require_once 'C:\Apache24\htdocs\vendor\autoload.php';
	$loader = new Twig_Loader_Filesystem('C:/Apache24/htdocs/templates');
	$twig = new Twig_Environment($loader, array());

	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());
	
	$catName = htmlspecialchars($_POST['name']);
	if(!empty($catName)){
		$query = "INSERT INTO catNames (name) VALUES ('".$catName."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "SELECT * FROM catNames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}

	echo $twig->render('cats.html', array('act' => 'cats',
		'rows' => $rows
		));
?>
		