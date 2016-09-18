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
		$query = "SELECT id, name, alias 
				FROM catnames
				WHERE parent IS NULL";
		$res = mysql_query($query);
		while ($row = mysql_fetch_array($res)){
			$query = "SELECT name
					 FROM catnames
					 WHERE parent = ".$row['id'];
			$result = mysql_query($query);
			while($child = mysql_fetch_array($result)){
				$children[] = $child;
			}
			$arr = array('name'=>$row['name'], 'alias'=>$row['alias'], 'children'=>$children);
			$rows[] = $arr;
			$children = array();
		}

		echo $twig->render('index.html', array('title'=>'Главня',
			'categories'=>$rows));
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

		if(!isset($children)){
			$query = "SELECT p.name FROM products AS p
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
				WHERE p.alias = ".$request->item;
		$res = mysql_query($query);
		while($row = mysql_fetch_array($res)){
			$rows[] = $row;
		}
		echo $twig->render('item.html', array(
			'title'=>'Item',
			'name'=>$rows[0].productName,
			'rows'=>$rows));
	});

	$klein->respond('GET', '/item', function() use ($twig){
		echo $twig->render('item_test.html');
	});

	$klein->respond('GET', '/admin', function() use ($twig){
		
	});
	$klein->dispatch();
?>