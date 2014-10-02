<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$a = json_decode($data);


$uname=$a->uname;
$udesc=$a->udesc;
$ufb=$a->ufb;
$umail=$a->origin;

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');

$stmt = $db->prepare('UPDATE google_users SET u_name = :uname, u_desc = :udesc, u_fb= :ufb WHERE google_email = "'.$umail.'"');

$stmt->execute(array(':uname'=>$uname, ':udesc'=>$udesc, ':ufb'=>$ufb));



?>
