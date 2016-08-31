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
	
	$catId = htmlspecialchars($_POST['name']);
	$paramId = htmlspecialchars($_POST['param']);
	if(!empty($catId) && !empty($paramId)){
		$query = "INSERT INTO categories (cat, param) VALUES ('".$catId."', ".$paramId.")";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = 	"SELECT cat.id AS id,
				cats.name AS catName,
				param.name AS paramName
				FROM categories AS cat
				LEFT JOIN catNames AS cats
				ON (cat.cat = cats.id)
				LEFT JOIN params AS param
				ON (cat.param = param.id)";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}
	
	$query = "SELECT id, name FROM params";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$params[] = $row;
	}
	
	$query = "SELECT id, name FROM catnames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$cats[] = $row;
	}

	echo $twig->render('classes.html', array('act' => 'classes',
		'rows' => $rows,
		'params' => $params,
		'cats' => $cats
		));
?>