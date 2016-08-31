<?php
	require_once 'C:/Apache24/htdocs/vendor/autoload.php';
	$loader = new Twig_Loader_Filesystem('C:/Apache24/htdocs/templates');
	$twig = new Twig_Environment($loader, array());

	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());
	
	$prodName = htmlspecialchars($_POST['prodName']);
	$catName = htmlspecialchars($_POST['paramName']);
	$value = htmlspecialchars($_POST['value']);
	if(!empty($prodName) && !empty($catName) && !empty($value)){
		$query = "INSERT INTO productparams (product, cat, value) VALUES ('".$prodName."', '".$catName."', '".$value."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = 	"SELECT prodP.id AS id,
				prod.name AS prodName,
				cats.name AS catName,
				param.name AS paramName,
				prodP.value AS value,
				param.type AS type
				FROM productparams AS prodP
				LEFT JOIN products AS prod
				ON (prodP.product = prod.id)
				LEFT JOIN categories AS cat
				ON (prodP.cat = cat.id)
				LEFT JOIN params AS param
				ON (cat.param = param.id)
				LEFT JOIN catnames AS cats
				ON (cat.cat = cats.id)";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}
	
	$query = "SELECT id, name FROM products";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rowsP[] = $row;
	}

	$query = "SELECT id, name FROM catnames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rowsC[] = $row;
	}

	echo $twig->render('productParams.html', array('act' => 'goodsParams',
		'rows' => $rows,
		'rowsP' => $rowsP,
		'rowsC' => $rowsC
		));
?>