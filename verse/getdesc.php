<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);
$message="";
$children="";
$a=array();

/*$len=sizeof($objData->children);
//echo $objData->data;
// perform query or whatever you wish, sample:
//echo $objData->children;


foreach($objData->children as $t){
$children=$children.substr($t,0,4);
}*/

//echo $objData->children;

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');
$stmt = $db->query('SELECT message FROM `messages` where mail="'.$objData->mail.'" and children="'.$objData->children.'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val)
		$message= $col_val['message'];
		
}



$stmt = $db->query('SELECT * FROM `google_users` where google_email="'.$objData->mail.'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val)
		$a[]=array("name"=>$col_val['google_name'], "link"=>$col_val['google_picture_link'], "desc"=>$col_val['u_desc'], "msg"=>$message);
}

echo json_encode($a);

?>



