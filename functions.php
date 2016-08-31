<?php
  function translit($s) {
    $s = (string) $s; // преобразуем в строковое значение
    $s = strip_tags($s); // убираем HTML-теги
    $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
    $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
    $s = trim($s); // убираем пробелы в начале и конце строки
    $s = mb_strtolower($s, 'utf-8'); // переводим строку в нижний регистр
    $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
    $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
    $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
    return $s; // возвращаем результат
  }

  function getChildren($parentId){
    $query = 'SELECT id, name, alias FROM catnames WHERE parent = "'.$parentId.'"';
    $res = mysql_query($query);
    $arr = array();
    while($row = mysql_fetch_array($res)){
      $children = getChildren($row['id']);
      if(count($children) > 0){
        $arr[] = array(
          'name' => $row['name'],
          'alias' => $row['alias'],
          'children' => $children
          );
      } else {
        $query =  'SELECT prodP.id AS id,
          prod.name AS prodName,
          cats.name AS catName,
          param.name AS paramName,
          prodP.value AS value,
          param.type AS type
          FROM productparams AS prodP
          INNER JOIN products AS prod
          ON (prodP.product = prod.id)
          INNER JOIN categories AS cat
          ON (prodP.cat = cat.id)
          INNER JOIN params AS param
          ON (cat.param = param.id)
          INNER JOIN catnames AS cats
          ON (cat.cat = cats.id AND cats.id = "'.$row['id'].'")';
        $res = mysql_query($query);
        $tabRows = array();
        while($tabRow = mysql_fetch_array($res)){
          $tabRows[] = $tabRow;
        }

        $arr[] = array(
          'name' => $row['name'],
          'alias' => $row['alias'],
          'table' => $tabRows
          );
      }
    }
    return $arr;
  }
?>