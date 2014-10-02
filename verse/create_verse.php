<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

require_once 'swiftmailer/swift_required.php';

require('disqusapi/disqusapi.php');

echo "first";

// configuration of smtp

$transport = Swift_SmtpTransport::newInstance('ssl://smtp.mailgun.org', 465);
$transport->setUsername("postmaster@sandboxb0fefa53992a47adb33a408da25c8886.mailgun.org");
$transport->setPassword("hari");

echo "second";

$data = file_get_contents("php://input");

$a = json_decode($data);


$title=$a->title;
$desc=$a->desc;
$origin=$a->origin;
$mails=$a->mails;
$pieces = array_unique(array_map('trim',explode(',',$mails)));//explode(",", $mails);

if (($key = array_search($origin, $pieces)) !== false) {
    unset($pieces[$key]);
}

$flag=1; // if $a->val is not enabled all are private;

//for Disq

$disqus = new DisqusAPI("PCI1V6gJ1XB6un7TJn2xXRREpvoSg2tCVeoJFbFus1HvOBsYgB4TTJedp4Y953Jm");

$api_key="3CkLP04cPgqqI3xiD5SJtvNjPHhvhry3M1nx1wZJWmqJ2voiTqHnXsRLKR8GBigf";
$website="http://theverse.io/verse/";
$disqname=$title;
$temp=$title.$origin;
$short_name=preg_replace('/[^a-zA-Z0-9]+/', '', $temp);
echo $short_name;
$access_token="ace121cce9d04a6dbaf3c5dd10e0981b";

print_r($disqus->forums->create(array(
    'api_key' => $api_key,
    'website' => $website,
    'name' => $disqname,
    'short_name' => $short_name,
    'access_token' => $access_token,
)));




echo "third";

// for swift mailer
$email = $origin;
// Create the message
$message = Swift_Message::newInstance();

//$message->setCc(array("cc_email1@gmail.com" => "Name NAme" , "cc_email2@gmail.com" => "His Name"));

$message->setSubject("You are invited to join a verse");

$message->setBody("hey, I welcome you to join the verse  theverse.io");

$message->setFrom($email, $email);

$message->setReplyTo(array($email => "info@verse.com"));



//echo $objData->data;
// perform query or whatever you wish, sample:

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');

$stmt = $db->prepare('INSERT INTO verse(title, cause, origin, flag) values(:title,:cause, :origin, :flag)');

$stmt->execute(array(':title'=>$title, ':cause'=>$desc, ':origin'=>$origin, ':flag'=>$flag));

$vid=$db->lastInsertId();

$stmt = $db->prepare('INSERT INTO tree(vid, name, parent) values(:vid,:name,:parent)');

$stmt->execute(array(':vid'=>$vid, ':name'=>$origin, ':parent'=>0));

$pid = $db->lastInsertId();

$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';

foreach($pieces as $m){

//$m=trim($m);

if(preg_match($regex, $m) && $m != $origin)
{ 
/*
$stmt = $db->prepare('select count(*) from tree where vid='.$vid.'and name="'.$m.'"');

$stmt->execute();

$r=$stmt->fetch(PDO::FETCH_ASSOC);

if( $r["count(*)"] == 0){

echo "inside duplicate elimination";
*/
$stmt = $db->prepare('INSERT INTO tree(vid, name, parent) values(:vid,:name,:parent)');

$stmt->execute(array(':vid'=>$vid, ':name'=>$m, ':parent'=>$pid));

//sending mails
$message->setTo(array($m => "Verse"));
$mailer = Swift_Mailer::newInstance($transport);
$mailer->send($message);

//}
}
}

?>
