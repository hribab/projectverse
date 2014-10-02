<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);
$a=array();

//echo $objData->data;
// perform query or whatever you wish, sample:

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');

$stmt = $db->query('SELECT * from verse where id='.$objData->data.'');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val)
		
		$a[]=array("title"=>$col_val['title'], "cause"=>$col_val['cause'], "origin"=>$col_val['origin']);
		
		
			
		
		}

		echo json_encode($a);


?>