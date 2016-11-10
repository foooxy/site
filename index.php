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

	$klein->respond('GET', '/admin', function($request) use ($twig){
		$query = "SELECT pp.value, p.name AS pName, p.alias AS pAlias, cn.name AS cName, pr.name AS prName, pr.type, p.id AS pid, pr.id AS prId
		FROM products AS p
		LEFT JOIN productparams AS pp
		ON pp.product = p.id
		LEFT JOIN categories AS c
		ON pp.cat = c.id
		LEFT JOIN catnames AS cn
		ON c.cat = cn.id
		LEFT JOIN params AS pr
		ON c.param = pr.id";

		$res = mysql_query($query);
		$curPid = -1;
		$first = true;
		$items = array();
		while($row = mysql_fetch_array($res)){
			if($curPid != $row['pid']){
				if($first){
					$first = false;
				} else {
					$items[] = array(
						'id'=>$curPid,
						'name'=>$pName,
						'alias'=>$pAlias,
						'cat'=>$cat,
						'params'=>$params
					);
					$params = array();
				}
				$curPid = $row['pid'];
				$pName = $row['pName'];
				$pAlias = $row['pAlias'];
				$cat = $row['cName'];
			}
			$params[] = array(
				'id'=>$row['prId'],
				'name'=>$row['prName'],
				'type'=>$row['type'],
				'value'=>$row['value']
			);
		}
		$items[] = array(
						'id'=>$curPid,
						'name'=>$pName,
						'alias'=>$pAlias,
						'cat'=>$cat,
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
		$query = "SELECT pp.value, param.id AS paramId
		FROM productparams AS pp
		LEFT JOIN categories AS c
		ON pp.cat = c.id
		LEFT JOIN params AS param
		ON c.param = param.id
		LEFT JOIN products AS p
		ON pp.product = p.id
		WHERE p.id=".$request->id;
		$res = mysql_query($query);
		$row = mysql_fetch_array($res);

		$query = "SELECT p.id, p.name, p.type 
				  FROM productparams AS pp
				  LEFT JOIN categories AS c
				  ON pp.cat = c.id
				  LEFT JOIN params AS p
				  ON c.param = p.id
				  WHERE pp.id=".$request->id;
		$res = mysql_query($query);
		$params = array();
		while($param = mysql_fetch_array($res)){
			if($param['id'] == $row['paramId']){
				$param['selected'] = 'true';
			} else {
				$param['selected'] = 'false';
			}

			$params[] = $param;
		}

		echo $twig->render('editParam.html', array(
			'param'=>$row['paramId'],
			'value'=>$row['value'],
			'params'=>$params
			));
	});
	$klein->respond('GET', '/admin/products/addParam', function($request) use ($twig){

		if($request->param('p', -1) == -1){
			$query = "SELECT pm.id, pm.name, pm.type
					  FROM products AS p
					  LEFT JOIN productparams AS pp
					  ON pp.product = p.id
					  LEFT JOIN categories AS c
					  ON pp.cat = c.id
					  LEFT JOIN params AS pm
					  ON c.param = pm.id
					  WHERE p.id=".$request->param('id');
			$res = mysql_query($query);

			$rows = array();
			if(mysql_num_rows($res) > 0){
				while($row = mysql_fetch_array($res)){
					$rows[] = $row;
				}
			}

			echo $twig->render('editParam.html', array(
				'params'=>$rows
				));
		} else {
			$query = "INSERT INTO productparams (cat, value, product) VALUES ('', '', '')";
		}
	});

	$klein->respond('GET', '/admin/products/addProduct', function($request) use ($twig){
		$name = $request->param('pn', -1);
		$cat = $request->param('c', -1);
		$alias = $request->param('a', -1);

		if($name == -1){
			$query = 'SELECT name, id FROM catnames';
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
			$query = "INSERT INTO products (name, alias) VALUES ('".$name."', '".$alias."')";
			mysql_query($query);
			$product = mysql_insert_id();

			$query = "SELECT id FROM categories WHERE cat = ".$cat;
			$res = mysql_query($query);
			while($row = mysql_fetch_array($res)){
				$query = "INSERT INTO productparams (value, cat, product) values ('0', '".$row['id']."', '".$product."')";
				mysql_query($query);
			}	
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
		$id = $request->param('id');
		$value = $request->param('v');
		
		$query = "UPDATE productparams SET value = '".$value."'";
		$res = mysql_query($query);
		echo $res;	
	});

	$klein->respond('GET', '/admin/cats', function($request) use ($twig){
		$reload = $request->param('reload', -1);
			$query = "SELECT cn.name AS cName, cn.alias, p.name AS pName, cn.id AS cId, p.id AS pId, p.type, c.id AS catId
				FROM catnames AS cn
				LEFT JOIN categories AS c
				ON c.cat = cn.id
				LEFT JOIN params AS p
				ON c.param = p.id
				ORDER BY cId";

			$res = mysql_query($query);
			$curId = -1;
			$first = true;
			$cats = array();
			while($row = mysql_fetch_array($res)){
				if($curId != $row['cId']){
					if($first){
						$first = false;
					} else {
						$cats[] = array(
							'id'=>$curId,
							'catId'=>$catId,
							'name'=>$cName,
							'alias'=>$cAlias,
							'params'=>$params
							);
						$params = array();
					}
					$curId = $row['cId'];
					$catId = $row['catId'];
					$cName = $row['cName'];
					$cAlias = $row['alias'];
				}
				$params[] = array(
					'pid'=>$row['pId'],
					'name'=>$row['pName'],
					'alias'=>$row['type']
					);
			}
			$cats[] = array(
				'id'=>$curId,
				'catId'=>$catId,
				'name'=>$cName,
				'alias'=>$cAlias,
				'params'=>$params
				);
			
			if ($reload == -1){
				echo $twig->render('adminCat.html', array(
					'items'=>$cats
					));
			} else {
				echo $twig->render('adminCatReload.html', array(
					'items'=>$cats
					));
			}	
	});
	$klein->respond('GET', '/admin/cats/addCat', function($request) use ($twig){
		$catName = $request->param('n', '');
		$catAlias = $request->param('a', '');

		if(empty($catName) || empty($catAlias)){
			echo $twig->render('adminAddCat.html');
		} else {
			$query = "INSERT INTO catnames (name, alias) VALUES ('".$catName."', '".$catAlias."')";
			mysql_query($query);
		}
	});
	$klein->respond('GET', '/admin/cats/delCat', function($request){
		$id = $request->param('id', -1);

		$query = "DELETE FROM catnames WHERE id = ".$id;
		mysql_query($query);
		$query = "DELETE FROM categories WHERE cat = ".$id;
		mysql_query($query);
	});
	$klein->respond('GET', '/admin/cats/editCat', function($request) use ($twig){
		$id = $request->param('id', -1);
		$name = $request->param('n', '');
		$alias = $request->param('a', '');

		if(empty($name) || empty($alias)){
			$query = "SELECT name, alias FROM catnames WHERE id = ".$id;
			$res = mysql_query($query);
			$row = mysql_fetch_array($res);

			echo $twig->render('adminAddCat.html', array(
				'name'=>$row['name'],
				'alias'=>$row['alias']
				));
		} else {
			$query = "UPDATE catnames
					  SET name = '".$name."', alias = '".$alias."'
					  WHERE id = ".$id;
			mysql_query($query);
		}
	});
	$klein->respond('GET', '/admin/cats/editParam', function($request) use ($twig){
		$cid = $request->param('id', ''); //id catnames
		$sPId = $request->param('p', ''); //id выбранного в форме параметра
		$catId = $request->param('catid', ''); // id categories

		if(empty($sPId)){
			$query = "SELECT * FROM params";
			$res = mysql_query($query);
			$params = array();
			while($row = mysql_fetch_array($res)){
				if($row['id'] == $sPId){
					$params[] = array(
						'id'=>$row['id'],
						'name'=>$row['name'],
						'type'=>$row['type'],
						'selected'=>'true'
						);
				} else {
					$params[] = array(
						'id'=>$row['id'],
						'name'=>$row['name'],
						'type'=>$row['type'],
						'selected'=>'false'
						);
				}
			}

			echo $twig->render('adminSelectParam.html', array('params'=>$params));
		} else {
			if(!empty($catId)){
				$query = "UPDATE categories SET param = '".$sPId."' WHERE id = ".$catId;
			} else {
				$query = "INSERT INTO categories (cat, param) VALUES ('".$cid."', '".$sPId."')";
			}
			mysql_query($query);
		}
	});
	$klein->respond('GET', '/admin/cats/addParam', function($request) use ($twig){
			$cid = $request->param('id', '');
			$param = $request->param('p', '');

			if (empty($cid) && empty($param)){
				$query = "SELECT * FROM params";
				$res = mysql_query($query);
				$params = array();
				while($row = mysql_fetch_array($res)){
					$params[] = $row;
				}

				echo $twig->render('adminSelectParam.html', array('params'=>$params));
			} else {
				$query = "INSERT INTO categories (cat, param) VALUES ('".$cid."', '".$param."')";
				mysql_query($query);
			}
	});
	$klein->respond('GET', '/admin/cats/delParam', function($request){
		$pid = $request->param('pid', '');
		$cid = $request->param('cid', '');

		$query = "DELETE FROM categories WHERE cat = ".$cid." AND param = ".$pid;
		mysql_query($query);
	});


	$klein->respond('GET', '/admin/products/delParam', function($request){

		$query = "SELECT pp.id
				FROM productparams AS pp
				WHERE pp.product IN 
				(SELECT p.id
				FROM productparams AS pp
				LEFT JOIN products AS p
				ON pp.product = p.id
				WHERE pp.id=".$request->param('id').")";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 1){ //проверяем, что параметр не является последним
			$query = "DELETE FROM productparams WHERE id=".$request->param('id');
			$res = mysql_query($query);
		}
	});

	$klein->respond('GET', '/admin/params', function($request) use ($twig){
		
		$query = "SELECT id, name, type FROM params";
		$res = mysql_query($query);
		$rows = array();
		if(mysql_num_rows($res) > 0){
			while($row = mysql_fetch_array($res)){
				$rows[] = $row;
			}
		}

		if($request->param('reload', -1) == -1){
			echo $twig->render('adminParam.html', array('items'=>$rows));
		} else {
			echo $twig->render('adminParamReload.html', array('items'=>$rows));
		}	
	});
	$klein->respond('GET', '/admin/params/addParam', function($request) use ($twig){

		$name = $request->param('n', -1);
		$type = $request->param('t', -1);

		if($name == -1){
			echo $twig->render('adminAddParam.html');
		} else {
			$query = "INSERT INTO params (name, type) VALUES ('".$name."', '".$type."')";
			mysql_query($query) or die(mysql_error());
		}
	});
	$klein->respond('GET', '/admin/params/editParam', function($request) use ($twig){

		$paramId = $request->param('id', -1);
		$paramName = $request->param('n', '');
		$paramType = $request->param('t', '');

		if(empty($paramName) && empty($paramType)){
			$query = "SELECT name, type FROM params WHERE id = ".$paramId;
			$res = mysql_query($query);
			$row = mysql_fetch_array($res);

			echo $twig->render('adminAddParam.html', array(
				'name'=>$row['name'],
				'type'=>$row['type']));
		} else {
			$query = "UPDATE params 
					  SET name = '".$paramName."', type = '".$paramType."'
					  WHERE id = ".$request->param('id');
			$res = mysql_query($query);
		}
	});
	$klein->respond('GET', '/admin/params/delParam', function($request){
		$paramId = $request->param('id', -1);

		$query = "DELETE FROM params WHERE id = ".$paramId;
		mysql_query($query);
	});
	$klein->respond('GET', '/free', function(){
		$query = 'INSERT INTO productparams (value, cat, product) values ("0.1", "20", "10")';
		mysql_query($query);
	});
	$klein->dispatch();
?>