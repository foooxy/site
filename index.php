<?php
	require_once __DIR__ . '/vendor/autoload.php';
	$loader = new Twig_Loader_Filesystem('./templates');
	$twig = new Twig_Environment($loader, array());

	/*$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";*/

	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

	$host = $url["host"];
	$user = $url["user"];
	$pass = $url["pass"];
	$db = substr($url["path"], 1);

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

	$klein->respond('GET', '/admin', function($request) use ($twig){
		$query="SELECT p.id, pp.id AS ppId, pp.value, p.name AS productName, p.alias, cn.name AS category, params.name, params.type FROM productparams AS pp LEFT JOIN products AS p ON pp.product = p.id LEFT JOIN categories AS cat ON pp.cat = cat.id LEFT JOIN catnames AS cn ON cat.cat = cn.id LEFT JOIN params ON cat.param = params.id ORDER BY pp.id";
		$res = mysql_query($query);
		$items = array();
		$params = array();
		$productName = '';
		$category = '';
		$alias = '';
		$paramId = -1;
		$current = -1;
		$first = true;

		while($row = mysql_fetch_array($res)){			
			if($first){
				$first = false;
				$currentId = $row['id'];
			}

			if($currentId != $row['id']){
				$currentId = $row['id'];
				$items[] = array(
					'id'=>$productId,
					'productName'=>$productName,
					'category'=>$category,
					'alias'=>$alias,
					'params'=>$params
				);
				$params = array();
			}

			$params[] = array(
						'id'=>$row['ppId'],
						'name'=>$row['name'],
						'type'=>$row['type'],
						'value'=>$row['value']
						);
			$productId = $row['id'];
			$productName = $row['productName'];
			$category = $row['category'];
			$alias = $row['alias'];
			$paramId = $row['ppId'];
		}
		$items[] = array(
					'id'=>$productId,
					'productName'=>$productName,
					'category'=>$category,
					'alias'=>$alias,
					'params'=>$params
				);

		if($request->param('reload', -1) == -1){
			echo $twig->render('admin.html', array(
				'items'=>$items
			));
		} else {
			echo $twig->render('adminReload.html', array(
				'items'=>$items
				));
		}	
	});
	$klein->respond('GET', '/admin/products/editParam/[:id]', function($request) use ($twig){
		$query = "SELECT pp.value, param.name AS paramName, param.type
		FROM productparams AS pp
		LEFT JOIN categories AS c
		ON pp.cat = c.id
		LEFT JOIN params AS param
		ON c.param = param.id
		WHERE pp.id=".$request->id;
		$res = mysql_query($query);
		$row = mysql_fetch_array($res);

		echo $twig->render('editParam.html', array(
			'param'=>$row['paramName'],
			'value'=>$row['value'],
			'type'=>$row['type']
			));
	});
	$klein->respond('GET', '/admin/products/addProduct', function($request) use ($twig){
		if($request->param('pn', -1) == -1){
			$query = 'SELECT name, alias FROM catnames';
			$res = mysql_query($query);
			while($cat = mysql_fetch_array($res)){
				$cats[] = $cat;
			}

			echo $twig->render('editProduct.html', array(
				'productName'=>'',
				'alias'=>'',
				'cats'=>$cats
				));
		} else {
			$query = "INSERT INTO products (name, alias) VALUES ('".$request->param('pn')."', '".$request->param('a')."')";
			mysql_query($query);
			$product = mysql_insert_id();
			$query = "SELECT id FROM catnames WHERE alias='".$request->param('c')."' LIMIT 1";
			$res = mysql_query($query);
			$cat = mysql_fetch_array($res);

			$query = "INSERT INTO categories (cat, param) 
			SELECT '".$cat['id']."', id 
			FROM products 
			LIMIT 1";

			mysql_query($query);
			$category = mysql_insert_id();
			echo $category;

			$query = "INSERT INTO productparams (cat, value, product) VALUES ('".$category."', '0', '".$product."')";
			$res = mysql_query($query);
		}
	});

	$klein->respond('GET', '/admin/products/delProduct', function($request){

		$query = "SELECT id, cat FROM productparams WHERE product=".$request->param('id');
		$res = mysql_query($query);
		$ppIds = '';
		$cIds = array();
		while($row = mysql_fetch_array($res)){
			$ppIds .= $row['id'].', ';
			$cIds[] = $row['cat'];
		}
		$ppIds = substr($ppIds, 0, -2);
		$cIds = array_unique($cIds);
		$cIdsS = '';
		for ($i = 0; $i < count($cIds); $i++){
			$cIdsS .= $cIds[$i];
			if ($i < count($cIds) - 1){
				$cIdsS .= ', ';	
			}
		}

		echo $ppIds;
		$query = "DELETE FROM categories WHERE id IN (".$cIdsS.")";
		mysql_query($query);
		$query = "DELETE FROM productparams WHERE id IN (".$ppIds.")";
		mysql_query($query);
		$query = "DELETE FROM products WHERE id=".$request->param('id');
		mysql_query($query);


	});

	$klein->respond('GET', '/admin/products/editProduct', function($request) use ($twig){
		if($request->param('pn', -1) == -1){
			$query = "SELECT p.name AS productName, p.alias, cn.alias AS catAlias 
			FROM productparams AS pp
			LEFT JOIN products AS p
			ON pp.product = p.id
			LEFT JOIN categories AS c
			ON pp.cat = c.id
			LEFT JOIN catnames AS cn
			ON c.cat = cn.id
			WHERE pp.id=".$request->param('id', -1);
			$res = mysql_query($query);
			$row = mysql_fetch_array($res);
			
			$query = 'SELECT name, alias FROM catnames';
			$res = mysql_query($query);
			while($cat = mysql_fetch_array($res)){
				if($cat['alias'] == $row['catAlias']){
					$cat['selected'] = '1';
				} else {
					$cat['selected'] = '0';
	 			}
				$cats[] = $cat;
			}

			echo $twig->render('editProduct.html', array(
				'productName'=>$row['productName'],
				'alias'=>$row['alias'],
				'cats'=>$cats
				));
		} else {
			$query = "SELECT c.id 
			FROM categories AS c
			LEFT JOIN catnames AS cn
			ON c.cat = cn.id 
			WHERE cn.alias='".$request->param('c')."'";
			$res = mysql_query($query);
			$cat = mysql_fetch_array($res);
			$query = "UPDATE productparams AS pp, products AS p
			SET p.name='".$request->param('pn')."', p.alias='".$request->param('a')."', pp.cat='".$cat['id']."' 
			WHERE p.id = pp.product AND pp.id=".$request->param('id', -1);
			$res = mysql_query($query);
			echo $res;
		}
	});
	$klein->respond('GET', '/admin/products/editParam', function($request) use ($twig){
		
		$query = "UPDATE productparams AS pp, params AS p, categories AS c SET p.name = '".$request->param('p')."', p.type = '".$request->param('t')."', pp.value = '".$request->param('v')."' WHERE pp.cat = c.id AND c.param = p.id AND pp.id=".$request->param('id');
		$res = mysql_query($query);
		echo $res;	
	});

	$klein->respond('GET', '/admin/cats', function() use ($twig){
		$query = "SELECT cn.name AS cName, cn.alias, p.name AS pName, cn.id AS cId, p.id AS pId, p.type
				  FROM categories AS c
				  LEFT JOIN catnames AS cn
				  ON c.cat = cn.id
				  LEFT JOIN params AS p
				  ON c.param = p.id
				  ORDER BY cId";
		$res = mysql_query($query);
		$cats = array();
		$params = array();
		$currentId = -1;
		$first = true;

		while($row = mysql_fetch_array($res)){
			if($first){
				$first = false;
				$currentId = $row['cId'];
			}
			if($currentId != $row['cId']){
				$currentId != $row['cId'];
				$cats[] = array(
					'id'=>$cId,
					'name'=>$cName,
					'alias'=>$cAlias,
					'params'=>$params
					);
				$params = array();
			}
			$params[] = array(
					'id'=>$row['pId'],
					'name'=>$row['pName'],
					'alias'=>$row['type']
				);
			$cId = $row['cId'];
			$cName = $row['cName'];
			$cAlias = $row['alias'];
		}
		$cats[] = array(
					'id'=>$cId,
					'name'=>$cName,
					'alias'=>$cAlias,
					'params'=>$params
					);
		echo $twig->render('adminCat.html', array(
			'items'=>$cats
			));	
	});
	$klein->dispatch();
?>