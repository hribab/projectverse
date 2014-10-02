<!DOCTYPE html>
<html ng-app>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="../../../dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js"></script>
	<script src="http://code.angularjs.org/angular-1.0.0.min.js"></script>
	<script src="search.js"></script>
	<script src="dndTree.js"></script>
<style type="text/css">
  
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

</style>

	</head>



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
          <a class="navbar-brand" href="#">Verse</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a href="/public/" active="true">Public Verses</a></li>
			<li><a data-toggle="modal" data-target="#myModal">Create Verse</a></li>
            <li><a data-toggle="modal" data-target="#myModal2">Profile</a></li>
            <li><a href="#">Logoff</a></li>
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
       <input type="text" class="form-control" placeholder="title of the verse"><br>
	   <span style="font-weight: bold;">Description: </span>
	   <textarea rows="4" cols="50" class="form-control">Be specific on the cause 
</textarea><br>
<span style="font-weight: bold;">Your Name: </span>
	   
       <input type="text" class="form-control" placeholder="Origin name"><br>
<span style="font-weight: bold;">Mail List: </span>
	   <textarea rows="4" cols="50" class="form-control">Mail list with comma separated Example: leela@gamil.com, rani@gmail.com, hari@buywaylink.com... 
</textarea><br>
      </div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Create Verse</button>
      </div>
    </div>
  </div>
</div>
	
	
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Let world know who you are.</h4>
      </div>
      <div class="modal-body">
	  <div class="input-group">
	  <span style="font-weight: bold;">Name: </span>
       <input type="text" class="form-control" placeholder="Name"><br>
	   <span style="font-weight: bold;">What your do: </span>
	   <textarea rows="4" cols="50" class="form-control">Let us know if you have good money to invest in verse
</textarea><br>
<span style="font-weight: bold;">Facebook link: </span>
	   <input type="text" class="form-control" placeholder="Your contacts is your asset"><br>
	   
       
      </div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save Details</button>
      </div>
    </div>
  </div>
</div>

  
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
      <img src="img/2.jpg" style="width: 300px; height: 300px;" alt="...">
      <div class="caption">
        <h3>Thumbnail label</h3>
        <p>...</p>
        <p><a href="#" class="btn btn-primary" style="align:  role="button">Select</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
      <img src="img/3.jpg" style="width: 300px; height: 300px;" alt="...">
      <div class="caption">
        <h3>Thumbnail label</h3>
        <p>...</p>
        <p><a href="#" class="btn btn-primary" role="button">Select</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
      <img src="img/4.jpg" style="width: 300px; height: 300px;" alt="...">
      <div class="caption">
        <h3>Thumbnail label</h3>
        <p>...</p>
        <p><a href="#" class="btn btn-primary" role="button">Select</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
      <img src="img/5.jpg" style="width: 300px; height: 300px;" alt="...">
      <div class="caption">
        <h3>Thumbnail label</h3>
        <p>...</p>
        <p><a href="#" class="btn btn-primary" role="button">Select</a></p>
      </div>
    </div>
  </div>
	
 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="../../../dist/js/bootstrap.min.js"></script>
    <script src="../../../assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>

