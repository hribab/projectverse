<?php

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'hari', 'hari');

$vid=17;
$stmt = $db->query('SELECT * from comments where vid="'.$vid.'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val){
	
	$verse_id[]=array("vid"=>$col_val['vid']);
		}
	}
	

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'hari', 'hari');

$stmt = $db->prepare('INSERT INTO verse(title, cause, origin, flag) values(:title,:cause, :origin, :flag)');

$stmt->execute(array(':title'=>$title, ':cause'=>$desc, ':origin'=>$origin, ':flag'=>$flag));

$vid=$db->lastInsertId();

$stmt = $db->prepare('INSERT INTO tree(vid, name, parent) values(:vid,:name,:parent)');

$stmt->execute(array(':vid'=>$vid, ':name'=>$origin, ':parent'=>0));

$pid = $db->lastInsertId();

$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';

foreach($pieces as $m){

$m=trim($m);



if(preg_match($regex, $m))
{ 

$stmt = $db->prepare('select count(*) from tree where vid='.$vid.'and name="'.$m.'"');

$stmt->execute();

$r=$stmt->fetch(PDO::FETCH_ASSOC);

if( $r["count(*)"] == 0){

$stmt = $db->prepare('INSERT INTO tree(vid, name, parent) values(:vid,:name,:parent)');

$stmt->execute(array(':vid'=>$vid, ':name'=>$m, ':parent'=>$pid));

//sending mails
$message->setTo(array($m => "Verse"));
$mailer = Swift_Mailer::newInstance($transport);
$mailer->send($message);

}
}
}

?>
