<?php
	require_once __DIR__ . '/vendor/autoload.php';
	$loader = new Twig_Loader_Filesystem('./templates');
	$twig = new Twig_Environment($loader, array());

	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());

	$klein = new \Klein\Klein();
	$klein->respond('GET', '/', function() use ($twig){
		$query = "SELECT name, alias 
				FROM catnames
				WHERE parent IS NULL";
		$res = mysql_query($query);
		while ($row = mysql_fetch_array($res)){
			$rows[] = $row;
		}

		echo $twig->render('index.html', array(
			'title'=>'Главная',
			'categories'=>$rows,
			'quantity'=>count($rows) / 3));
	});
	$klein->respond('GET', '/category/[:catname]', function($request) use ($twig){
		$query = "SELECT id, name FROM catnames WHERE alias = '".$request->catname."'";
		$res = mysql_query($query);
		$cat = mysql_fetch_array($res);

		$query = "SELECT name, alias FROM catnames WHERE parent = ".$cat['id'];
		$res = mysql_query($query);
		while($child = mysql_fetch_array($res)){
			$children[] = $child;
		}

		$items = array();
		if(!isset($children)){
			$query = "SELECT p.name, p.alias FROM products AS p
					  LEFT JOIN productparams AS pp
					  ON pp.product = p.id
					  LEFT JOIN categories AS cat 
					  ON pp.cat = cat.id
					  LEFT JOIN catnames AS catn
					  ON cat.cat = catn.id
					  WHERE catn.id = ".$cat['id'];
			$res = mysql_query($query);
			while($item = mysql_fetch_array($res)){
				$items[] = $item;
			}
		}
		
		echo $twig->render('category.html', array(
			'title'=>$cat['name'],
			'name'=>$cat['name'],
			'children'=>$children,
			'items'=>$items));
	});
	$klein->respond('GET', '/items/[:item]', function($request) use ($twig){
		$query = "SELECT p.name AS productName, params.name, params.type, pp.value FROM products AS p
				LEFT JOIN productparams AS pp
				ON pp.product = p.id
				LEFT JOIN categories AS c
				ON pp.cat = c.id
				LEFT JOIN params
				ON c.param = params.id
				WHERE p.alias = '".$request->item."'";
		$res = mysql_query($query);
		$productName = '';
		while($row = mysql_fetch_array($res)){
			$productName = $row['productName'];
			$rows[] = $row;
		}
		echo $twig->render('item.html', array(
			'title'=>$productName,
			'name'=>$productName,
			'rows'=>$rows));
	});

	$klein->respond('GET', '/admin', function() use ($twig){
		$query="SELECT p.id, pp.value, p.name AS productName, cn.name AS category, params.name, params.type
				FROM productparams AS pp
				LEFT JOIN products AS p
				ON pp.product = p.id
				LEFT JOIN categories AS cat
				ON pp.cat = cat.id
				LEFT JOIN catnames AS cn
				ON cat.cat = cn.id
				LEFT JOIN params
				ON cat.param = params.id
				ORDER BY pp.id";
		$res = mysql_query($query);
		$currentId = -1;
		$items = array();
		$params = array();
		$productName = '';
		$category = '';
		while($row = mysql_fetch_array($res)){
			$productName = $row['productName'];
			$category = $row['category'];
			if ($currentId != $row['id']){
				$currentId = $row['id'];
				if(count($params) > 0){
					$items[] = array(
						'id'=>$row['id'],
						'productName'=>$row['productName'],
						'category'=>$row['category'],
						'params'=>$params
						);
				}
			}
			$params[] = array(
						'name'=>$row['name'],
						'type'=>$row['type'],
						'value'=>$row['value']
				);
		}
		if(count($params) > 0){
			$items[] = array(
				'id'=>$currentId,
				'productName'=>$productName,
				'category'=>$category,
				'params'=>$params
				);
		}


		echo $twig->render('admin.html', array(
			'items'=>$items
			));	
	});
	$klein->dispatch();
?>