<?php
// sends a mail "you are removed"
// update tables

require_once 'swiftmailer/swift_required.php';

// configuration of smtp
$transport = Swift_SmtpTransport::newInstance('ssl://smtp.mailgun.org', 465);
$transport->setUsername("postmaster@sandboxb0fefa53992a47adb33a408da25c8886.mailgun.org");
$transport->setPassword("hari");

// receive data by post from search.js in rverse function
$data = file_get_contents("php://input");
$a = json_decode($data);

$vid=trim($a->vid);
$origin=trim($a->origin);

// for swift mailer
$email = $origin;
// Create the message
$message = Swift_Message::newInstance();

//$message->setCc(array("cc_email1@gmail.com" => "Name NAme" , "cc_email2@gmail.com" => "His Name"));

$message->setSubject("You are removed from verse ");

$message->setBody("Hey, You are removed from a verse");

$message->setFrom($email, "Verse");

$message->setReplyTo(array($email => "info@verse.com"));

//sending mails
$message->setTo(array($email => "Verse"));
$mailer = Swift_Mailer::newInstance($transport);
$mailer->send($message);



$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');


// get id and parent id of current user
$stmt = $db->query('SELECT * FROM `tree` where vid='.$vid.' and name="'.$origin.'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val)
		{
	$id=$col_val['id'];
	$pid=$col_val['parent'];
	}
	}

// if parent id is 0, means we must remove verse and delete users
	if( $pid == 0){
		$stmt = $db->prepare('DELETE FROM `tree` WHERE vid = :vid and parent ='.$id.'');
		$stmt->execute(array(':vid'=>$vid));
	
	 //remove verse
		$stmt = $db->prepare('DELETE FROM `verse` WHERE id = :vid');
		$stmt->execute(array(':vid'=>$vid));
	
	}

else{

$stmt = $db->prepare('update `tree` set parent= :parentid where parent='.$id.' and vid='.$vid.'');
$stmt->execute(array(':parentid'=>$pid));

}
	
	
//remove the user from verse
$stmt = $db->prepare('DELETE FROM `tree` WHERE vid = :vid and name ="'.$origin.'"');
$stmt->execute(array(':vid'=>$vid));


?>
