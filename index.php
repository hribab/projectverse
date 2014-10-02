<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="verse">
    <link rel="icon" href="v.ico">

    <title>Verse</title>
<link rel="stylesheet" type="text/css" href="css/isotope.css" media="screen" />	
		<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap-theme.css">
        <link rel="stylesheet" href="css/style.css">
		<!-- skin -->
		<link rel="stylesheet" href="skin/default.css">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://theverse.io/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  </head>

  <body>

   
		<section id="header" class="appear"></section>
		<div class="navbar navbar-fixed-top" role="navigation" data-0="line-height:100px; height:100px; background-color:rgba(0,0,0,0.3);" data-300="line-height:60px; height:60px; background-color:rgba(0,0,0,1);">
			 <div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="fa fa-bars color-white"></span>
					</button>
					<h1><a class="navbar-brand" href="index.html" data-0="line-height:90px;" data-300="line-height:50px;">			Project Verse
					</a></h1>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav" data-0="margin-top:20px;" data-300="margin-top:5px;">
					
	  
	  
<?php

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id 		= '622778754398-lrch3lp9ft1q3ohndv78g77v8rm3dfjq.apps.googleusercontent.com';
$google_client_secret 	= 'AAo2HYBWY9OqeimZSo_ss9mu';
$google_redirect_url 	= 'http://theverse.io'; //path to your script
$google_developer_key 	= 'AIzaSyBuv8HL5R1_x2_j30UvTXQC8JbIq_asYEo';

########## MySql details (Replace with yours) #############
$db_username = "root"; //Database Username
$db_password = "V3r5e"; //Database Password
$hostname = "localhost"; //Mysql Hostname
$db_name = 'verse'; //Database Name
###################################################################

//include google api files
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';

//start session
session_start();

$gClient = new Google_Client();
$gClient->setApplicationName('Verse');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);

//If user wish to log out, we just unset Session variable
if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken();
	
	$mysqli = new mysqli($hostname, $db_username, $db_password, $db_name);
	
	if ($mysqli->connect_error) {
		die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}
	$mysqli->query('UPDATE google_users SET login = 0 WHERE google_email="'.$_SESSION['mail'].'"');
	
  header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL)); //redirect user back to page
}

//If code is empty, redirect user to google authentication page for code.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}

if (isset($_SESSION['token'])) 
{ 
	$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) 
{
	  //For logged in user, get details from google using access token
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $_SESSION['id']       = $user_id;
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $_SESSION['mail']		= $email;
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//For Guest user, get google login url
	$authUrl = $gClient->createAuthUrl();
}



if(isset($authUrl)) //user is not logged in, show login button
{	

echo '<li class="active"><a href="'.$authUrl.'">Login With Google</a><SPAN id="tips" STYLE="color: white; font-size: 8pt">Secured with OAuth</SPAN></li>';
} 
else // user logged in 
{
   /* connect to database using mysqli */
	$mysqli = new mysqli($hostname, $db_username, $db_password, $db_name);
	
	if ($mysqli->connect_error) {
		die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}
	
	//compare user id in our database
	$user_exist = $mysqli->query("SELECT COUNT(google_id) as usercount FROM google_users WHERE google_id=$user_id")->fetch_object()->usercount; 
	if($user_exist)
	{
	   $mysqli->query('UPDATE google_users SET login = 1 WHERE google_email="'.$email.'"');
   
	   header('Location: verse/');
		//echo '<li><a>'.$user_name.'!</a></li>';
	}else{ 
		//user is new
		//echo '<li> '.$user_name.' </li>';
		
		$mysqli->query("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link, login) 
		VALUES ($user_id, '$user_name','$email','$profile_url','$profile_image_url',1)");
		
		header('Location: verse/');
	}

	
	//echo '<li><a class="logout" href="?reset=1">Logout</a></li>';
	

}
 

?>
	  
          </ul>
				</div><!--/.navbar-collapse -->
			</div>
		</div>

		<section class="featured">
			<div class="container"> 
				<div class="row mar-bot40">
					<div class="col-md-6 col-md-offset-3">
						
						<div class="align-center">
							<i class="fa fa-users fa-5x mar-bot20"></i>
							<h2 class="slogan">Project Verse</h2>
							<p>
							Simple way to get things done.Proliferate requests through your social networks.
				
							</p>	
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<!-- services -->
		<section id="section-services" class="section pad-bot30 bg-white">
		<div class="container"> 
		
			<div class="row mar-bot40">
				<div class="col-lg-4" >
					<div class="align-center">
						<i class="fa fa-bars fa-5x mar-bot20"></i>
						<h4 class="text-bold">Viewable Graph</h4>
						<p>Invite friends to a verse to participate in a task or event. Those friends can invite more friends they think can contribute, forming a graph of the social network involved
						</p>
					</div>
				</div>
					
				<div class="col-lg-4" >
					<div class="align-center">
						<i class="fa fa-bullhorn fa-5x mar-bot20"></i>
						<h4 class="text-bold">Expanding</h4>
						<p>Anyone in the verse can invite more people to the verse. Any verse has the potential to become viral
						</p>
					</div>
				</div>
			
				<div class="col-lg-4" >
					<div class="align-center">
						<i class="fa fa-comments fa-5x mar-bot20"></i>
						<h4 class="text-bold">Discussion</h4>
						<p> Everyone on the graph can discuss the verse in a shared chat space
						</p>
					</div>
				</div>
			
			</div>	

		</div>
		</section>
			
		<!-- spacer section:testimonial -->
		<section id="testimonials" class="section" data-stellar-background-ratio="0.5">
		<div class="container">
			<div class="row">				
					<div class="col-lg-12">
							<div class="align-center">
										<div class="testimonial pad-top40 pad-bot40 clearfix">
											<h5>
													As a founder, I personally use VERSE to get introductions to Angle investors											</h5>
											<br/>
											<span class="author">&mdash; Hari </span>
										</div>

								</div>
							</div>
					</div>
				
			</div>	
		</div>	
		</section>
			
		<!-- about -->
		<section id="section-about" class="section appear clearfix">
		<div class="container">

				<div class="row mar-bot40">
					<div class="col-md-offset-3 col-md-6">
						<div class="section-header">
							<h2 class="section-heading animated" data-animation="bounceInUp">The Team</h2>
							<p>To get this done, It took bunch of starbucks soy latte and long hours of time at various starbucks cafe. Thanks starbucks !! </p>
						</div>
					</div>
				</div>

					<div class="row align-center mar-bot40">
						<div class="col-md-3">
						</div>
						<div class="col-md-3">
							<div class="team-member">
								<figure class="member-photo"><img src="img/team/member1.jpg" alt="" /></figure>
								<div class="team-detail">
									<h4>Hari</h4>
									<span>Developer & Researcher</span></br>
									<span>Contact me on hribab@gmail.com</span>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="team-member">
								<figure class="member-photo"><img src="img/team/member2.jpg" alt="" /></figure>
								<div class="team-detail">
									<h4>Leela</h4>
									<span>Socialist</span>
								</div>
							</div>
						</div>
					</div>
						
		</div>
		</section>
		<!-- /about -->
		
		<!-- spacer section:stats -->
		<section id="parallax1" class="section pad-top40 pad-bot40" data-stellar-background-ratio="0.5">
			<div class="container">
            <div class="align-center pad-top40 pad-bot40">
                <blockquote class="bigquote color-white">Contacts that matter</blockquote>
				<p class="color-white">Use verse to make use of contacts for a cause</p>
            </div>
			</div>	
		</section>
		 
	<a href="#header" class="scrollup"><i class="fa fa-chevron-up"></i></a>	

	<script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
	<script src="js/jquery.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.isotope.min.js"></script>
	<script src="js/jquery.nicescroll.min.js"></script>
	<script src="js/fancybox/jquery.fancybox.pack.js"></script>
	<script src="js/skrollr.min.js"></script>		
	<script src="js/jquery.scrollTo-1.4.3.1-min.js"></script>
	<script src="js/jquery.localscroll-1.2.7-min.js"></script>
	<script src="js/stellar.js"></script>
	<script src="js/jquery.appear.js"></script>
	<script src="js/validate.js"></script>
    <script src="js/main.js"></script>
        	
	</body>
</html>

