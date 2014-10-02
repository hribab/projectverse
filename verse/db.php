<?php
// Connecting, selecting database

// Performing SQL query
//$stmt = $db->query('SELECT t1.name AS lev1, t2.name as lev2, t3.name as lev3, t4.name as lev4 FROM tree AS t1 LEFT JOIN tree AS t2 ON t2.parent = t1.id LEFT JOIN tree AS t3 ON t3.parent = t2.id LEFT JOIN tree AS t4 ON t4.parent = t3.id WHERE t1.name = "ELECTRONICS"');

$b=array();
$flag=1;

function func($id, $sd){

$a=array();

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');

$stmt = $db->query('SELECT * from tree where parent='.$id.' and vid='.$sd.'');

if(isset($stmt)){

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
foreach ($line as $col_val)
$a[]=array("name"=>$col_val['name'],"children"=>func($col_val['id'], $_GET['hari']));

}

}

return $a;

}

$b[]=func(0,$_GET['hari']);

echo trim(json_encode($b),"[[]]");
// Closing connection

?>