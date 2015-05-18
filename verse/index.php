<!DOCTYPE html>
<html ng-app>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../v.ico">

    <title>Project Verse</title>

    <!-- Bootstrap core CSS -->
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
	<link rel="stylesheet" href="dist/css/d3-bootstrap-plugins.min.css">


    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://code.angularjs.org/angular-1.0.0.min.js"></script>
<script src="search.js"></script>
<link rel="stylesheet" href="dist/css/d3-bootstrap-plugins.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.11/d3.min.js"></script>
<script src="dist/js/d3-bootstrap-plugins.js"></script>
<script>
$(function(){
	$("#close1").click(function(){
		$("#tree-container").load();
		console.log("in refresh");
		})
	})
	
</script>

<script>
if(typeof DISQUS === 'undefined'){

(function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://dkeixm.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })(); };
   	
</script>
<script>
$("#remverse").click(function() {
    $(this).after(
        '<div class="alert alert-success alert-dismissable">'+
            '<button type="button" class="close" ' + 
                    'data-dismiss="alert" aria-hidden="true">' + 
                '&times;' + 
            '</button>' + 
            'You removed from verse' + 
         '</div>');
}); 
</script>


	
<style type="text/css">
  
  .CattoBorderRadius
{
     width: 50px;
     height: 50px;
    border-radius: 50%;
}
  
	.node {
    cursor: pointer;
  }

  .overlay{
      background-color:#EEE;
  }
   
  .node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
  }
   
  .node text {
    font-size:10px; 
    font-family:sans-serif;
  }
   
  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  .templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
  }

  .ghostCircle.show{
      display:block;
  }

  .ghostCircle, .activeDrag .ghostCircle{
       display: none;
  }
 
.circle:hover{
-webkit-transform:scale(1.2);
    -moz-transform:scale(1.2);
    -o-transform:scale(1.2);
-webkit-transition-duration: 4s;
-moz-transition-duration: 4s;
-o-transition-duration: 4s;



}

</style>

	</head>


<?php
	session_start();
	
	if (!isset($_SESSION['id']) || !isset($_SESSION['mail']) ) {
		header('Location: http://theverse.io/?reset=1');
    	exit();
	}
	$uid=$_SESSION['id'];
	$mail=$_SESSION['mail'];
	$mail=trim($mail);
	
//	echo $mail;


$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', '****', '*****');

	

$stmt = $db->prepare('SELECT google_picture_link FROM `google_users` where google_email="'.$mail.'"');

$stmt->execute();

$r=$stmt->fetch(PDO::FETCH_ASSOC);
$pic_image=$r["google_picture_link"];



?>
	

  <body ng-controller="SearchCtrl">

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://theverse.io/verse/">Verse</a>
        
		</div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
		  
		<!--	<li><a href="#" id="popover1" rel="popover" data-content="It's so simple to create a tooltop for my website!" data-original-title="Twitter Bootstrap Popover" >Public Verses</a></li> -->
			<li><a data-toggle="modal" data-target="#myModal">Create Verse</a></li>
            <li><a data-toggle="modal" data-target="#myModal2">Profile</a></li>
			
            <li><a href="http://theverse.io/?reset=1">Logout</a></li>
			<?php echo '<li><img class="img-responsive CattoBorderRadius" src="'.$pic_image.'"/>'; ?>
			
		  </li>
          </ul>
          
        </div>
      </div>
    </div>
	
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Create a Verse</h4>
      </div>
      <div class="modal-body">
	  <div class="input-group">
	  <span style="font-weight: bold;">Title: </span>
       <input type="text" ng-model="verse.title" class="form-control" placeholder="title of the verse"><br>
	   <span style="font-weight: bold;">Description: </span>
	   <textarea ng-model="verse.desc" rows="4" cols="50" class="form-control"  placeholder="Be specific on the cause ">
</textarea><br>
<?php echo '<input id="origin" type="hidden" ng-model="verse.origin"  ng-init="verse.origin=\''.$mail.'\'">'; ?>	   
       
<span style="font-weight: bold;">Mail List: </span>
	   <textarea id="mail" ng-model="verse.mails" rows="4" cols="50" class="form-control" placeholder="Mail list with comma separated Example: leela@gamil.com, rani@gmail.com, hari@buywaylink.com..." >
</textarea><br>

       
          <!--    <label class="radio-inline">
          <input name="radioGroup" ng-model="verse.val" id="radio1" value="1" type="radio"> <span style="font-weight: bold;">Private</span>
        </label>
	
	<label class="radio-inline">
          <input name="radioGroup" ng-model="verse.val" id="radio2" value="0" checked="checked" type="radio"><span style="font-weight: bold;"> Public </span>
        </label>   -->
      
   
      </div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" ng-click="cverse()" class="btn btn-primary" data-dismiss="modal">Create Verse</button>
      </div>
    </div>
  </div>
</div>
	
	
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Let verse know who you are.</h4></br>
		
      </div>
      <div class="modal-body">
	  <div class="input-group">
	  <span style="font-weight: bold;">Name: </span>
       <input type="text" ng-model="verse.uname" class="form-control" placeholder="Name"><br>
	   <span style="font-weight: bold;">What you do: </span>
	   <textarea ng-model="verse.udesc" rows="4" cols="50" class="form-control">Let us know if you have good money to invest in verse
</textarea><br>
<span style="font-weight: bold;">Facebook link: </span>
	   <input type="text" ng-model="verse.ufb" class="form-control" placeholder="Your contacts is your asset"><br>
	   
       
      </div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" ng-click="cprofile()" class="btn btn-primary" data-dismiss="modal">Save Details</button>
      </div>
    </div>
  </div>
</div>


	<div class="container-fluid" >
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar" >
          <ul class="nav nav-sidebar">
		  
<?php

$db = new PDO('mysql:host=localhost;dbname=verse;charset=utf8', 'root', 'V3r5e');
$verse_id=array();

$stmt = $db->query('SELECT * from tree where name="'.$mail.'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val){
	
	$verse_id[]=array("vid"=>$col_val['vid']);
		}
	}
	
foreach ($verse_id as $b){
	
$stmt = $db->query('SELECT * from verse where id="'.$b['vid'].'"');

while($line = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach ($line as $col_val){
	
	//echo "<input type=\"hidden\" ng-model=\"keywords".$i."\" ng-value=\"keywords".$i++."='".$col_val['id']."'\"/>";
	
	echo "<li><a ng-click=\"search(".$col_val['id'].")\"  ng-modal=\"mails.vid\" >".$col_val['title']."</a></li>";
		}
	}
	}
?>

     </ul>
	
          </div>
		  
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" ng-show="!details">
		<!-- sas 
		<div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
		
		<img data-src="holder.js/200x200/auto/sky/text:Create A Verse" class="circle" alt="Generic placeholder thumbnail">
		</div>
		    <div class="col-xs-6 col-sm-3 placeholder">
		
	<img data-src="holder.js/200x200/auto/vine/text:Invite People" class="circle" alt="Generic placeholder thumbnail">
  	</div>
  	  <div class="col-xs-6 col-sm-3 placeholder">
		
	<img data-src="holder.js/200x200/auto/sky/text:Add contacts to Invited Verse" class="circle" alt="Generic placeholder thumbnail">
  	</div>
	  <div class="col-xs-6 col-sm-3 placeholder">
	
	<img data-src="holder.js/200x200/auto/vine/text:Join a Public Verse" class="circle" alt="Generic placeholder thumbnail">
  	</div>
		
		</div>
		<div class="jumbotron">
		<h3 align="center"><u>Use Cases </u> </h3></br>
<p>Get dance lessons</p>
Asking about dance lessons from a friend who may know dance instructors can quickly turn into action using Verse. A quick look at the graph will show the instructors who made the initial request, enabling them to contact you directly.

<p>Create a new supply chain</p>
What starts off as a simple request for food can be passed along to farmers, who can split it into requests for truck drivers and farm supplies. Wherever you spend money, you can try asking for it in Verse; giving them a chance to ask for whatever they want from their friend network.

<p>Run your campaign</p>
Volunteer-run organizations that thrive on social media can use Verse to virally reach out for support. Using Verse, a request to canvass a neighborhood can be split up into duties such as data entry and canvassing certain streets then passed to volunteer leaders and their extended friend network.

<p>Streamline your company</p>
Within Verse, corporate structure is made more friendly for everyone. A CEO who uses Verse to send requests to manage projects can have a better sense of how their company makes things happen. Workers who receive a request in Verse can see the bigger picture for the project they are working on, and coordinate with people working on similar projects to avoid redundancy.

<p>Shape everyday projects</p>
Verse can be used to communicate all kinds of needs to friends. Requests for various kinds of social events, from outings to parties, are all the more accessible when everyone can see who is doing what. Requests for particular items in need can be passed from friend group to friend group without losing track of who asked for it in the first place.


  </div> -->
  <div class="container-fluid">
   <div class="col-sm-3 col-md-2 " ></div>
   <div>
        
   <a  align="center" href="https://s3.amazonaws.com/easel.ly/all_easels/456012/1410409472/thumb.jpg"></a><img class="img-responsive" align="center" src="https://s3.amazonaws.com/easel.ly/all_easels/456012/1410409472/image.jpg" alt="1410409472" title="easel.ly" /></a><br />
	</div>	
  </div>
		</div>
	    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" ng-show="details">
           
		  <h1 class="page-header">{{title}}</h1>
		  
		  <p><span style="font-weight: bold;">Description: </span> {{cause}}
		  </p>
          <p><span style="font-weight: bold;">Origin: </span> {{origin}} 
		  </p>
          
		  
		  <div class="row placeholders">
		  <p><span style="font-weight: bold;">zoom-in:</span> double-click  <span style="font-weight: bold;"> Zoom-Out:</span>Shift+Double-Click  
		   
		  
		  <a data-toggle="modal" data-target="#myModal3" style="float: right;"> add more people </a></p>
            
			<div id="tree-container"></div>
			<a ng-click="remverse('<?php echo $_SESSION['mail']; ?>')" style="float: right;"> remove me </a></br>
			 <div  ng-show="ralert"><div class="alert alert-info alert-dismissible" role="alert">
  <strong>You are removed from this verse!</strong> What ever may be the reason you left this verse, Keep in touch with friends 
</div></div>
        
        
		<div id="disqus_thread"></div>
		  
        </div>
		
		
			
      </div>
	  
	  
	  <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add more contacts to the verse (,)</h4>
      </div>
      <div class="modal-body">
	  <div class="input-group">
	  <span style="font-weight: bold;">Mail List: </span>
	   <textarea rows="8" cols="70" ng-model="mails.addmails" class="form-control" placeholder="Example: verse@verse.com, anna@gmail.com, ...">
</textarea></br></br>
<span style="font-weight: bold;">Message to convey: </span>

<textarea rows="4" cols="70" ng-model="mails.message" class="form-control" placeholder="Hey, I like to include you in this thread ">
</textarea><br>
	   
       
      </div>
	  </div>
      <div class="modal-footer">
        <button id="close1" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		 <input id="origin" type="hidden" ng-model="mails.origin"  ng-init="mails.origin='<?php echo $mail;?>'">
        <button type="button" ng-click="averse('<?php echo $_SESSION['mail'];?>')" class="btn btn-primary" data-dismiss="modal">Add Emails</button>
      </div>
    </div>
  </div>
</div>
	  
	  
	  
	  
    </div>
	 
	</div>
	
 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="../dist/js/bootstrap.min.js"></script>
    <script src="../assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../assets/js/ie10-viewport-bug-workaround.js"></script>
	<!--tool tip js -->
	
	
  
  </body>
</html>

