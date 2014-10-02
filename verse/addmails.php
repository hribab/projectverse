<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

require_once 'swiftmailer/swift_required.php';
// configuration of smtp

$transport = Swift_SmtpTransport::newInstance('ssl://smtp.mailgun.org', 465);
$transport->setUsername("postmaster@sandboxb0fefa53992a47adb33a408da25c8886.mailgun.org");
$transport->setPassword("hari");

$data = file_get_contents("php://input");

$a = json_decode($data);

$mails=$a->addmails;
$vid=trim($a->vid);
$origin=trim($a->origin);
$children="";

$pieces = array_unique(array_map('trim',explode(',',$mails)));//explode(",", $mails);

// checking if user entered his own mail address, if he entered just remove it
if (($key = array_search($origin, $pieces)) !== false) {
    unset($pieces[$key]);
}



// for swift mailer
$email = $origin;

echo $email;
// Create the message
$message = Swift_Message::newInstance();

//$message->setCc(array("cc_email1@gmail.com" => "Name NAme" , "cc_email2@gmail.com" => "His Name"));

$message->setSubject("You are invited to join a verse");

$message->setBody("You are invited to a verse, theverse.io");

$message->setFrom($email, $email);

$message->setReplyTo(array($email => "info@verse.com"));

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');


$u_message=trim($a->message);

sort($pieces);

foreach($pieces as $t){

$t=trim($t);
echo $t;
$children=$children.substr($t,0,4);

}

echo $children;
//$children=strrev($children);
//echo $children;

//update messages set children=concat(children, 'gdpd') where mail="itzhari.g@gmail.com";

$stmt = $db->prepare('INSERT INTO messages(mail, children, message) values(:mail,:children,:message)');

$stmt->execute(array(':mail'=>$origin, ':children'=>$children, ':message'=>$u_message));

//$stmt = $db->prepare('UPDATE google_users SET message = :msgs WHERE google_email="'.$origin.'"');

//$stmt->execute(array(':msgs'=>$u_message));



$stmt = $db->query('SELECT id FROM `tree` where vid='.$vid.' and name="'.$origin.'"');


while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val)
		
    $pid=$col_val['id'];
		
	}

$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';

foreach($pieces as $m){

//$m=trim($m);

if(preg_match($regex, $m))
{ 
/*
$stmt = $db->prepare('select count(*) from tree where vid='.$vid.'and name="'.$m.'"');

$stmt->execute();

$r=$stmt->fetch(PDO::FETCH_ASSOC);
$a=$r["count(*)"];*/

$a= $db->query('select count(*) from tree where vid='.$vid.' and name="'.$m.'"')->fetchColumn();

if($a == 0){

echo "inserting-".$m;

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
