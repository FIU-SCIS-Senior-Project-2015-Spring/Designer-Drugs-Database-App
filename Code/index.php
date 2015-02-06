<!DOCTYPE html>
<html lang="en" ng-app="mainApp">
<head>
	<title>Designer drug database</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, no-cache, no-store, must-revalidate">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<script src="js/jquery-1.11.1.min.js"></script>
	<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">-->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.1/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.1/angular-route.js"></script>
	<!--<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.1/angular-resource.js"></script>-->	
	<script src="js/angular-file-upload-shim.min.js"></script> 
	<!--<script src="js/angular.min.js"></script>-->
	<!--<script src="js/angular-route.js"></script>-->
	<script src="js/angular-file-upload.min.js"></script> 
	<link href="css/styles.css" rel="stylesheet">
</head>			
<script src="functions/functions.js"></script>
	
<body class="Mybody">

	<!--navbar-->
	<nav class="navbar navbar-default"  ng-controller="ControllerNavbar">
	  <div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand" href="#">Designer drug database</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			  <ul class="nav navbar-nav navbar-right">
				<li class="active"><a href="#/home">Home</a></li>
				<li><a href="#/login" id="barLoginMenu">Login</a></li>
				<li><a href="#" ng-click="clickLogout()" class="hidden" id="barLogoutMenu">LogOut</a></li>
				<li><a href="#userMain" class="hidden" id="barAccountMenu">Account</a></li>
				<li><a href="#/about">About</a></li>
				<li><a href="#/contact">Contact</a></li>
			  </ul>
		</div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	
	<!--all pages will be thrown here-->
	<div id="main">
        <div ng-view></div>
    </div>
			
	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
	<script src="js/bootstrap.js"></script>
</body>
</html>