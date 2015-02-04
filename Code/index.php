<!DOCTYPE html>
<html lang="en" ng-app="mainApp">
<head>
	<title>Designer drug database</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, no-cache, no-store, must-revalidate">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	
	<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">-->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!--<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.5/angular.min.js"></script>-->
	<script src="js/angular-file-upload-shim.min.js"></script> 
	<script src="js/angular.min.js"></script>
	<script src="js/angular-route.js"></script>
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
				<li class="active"><a href="#home" ng-click="clickHome()">Home</a></li>
				<li><a href="#login" ng-click="clickLogin()" id="barLoginMenu">Login</a></li>
				<li><a href="#logout" ng-click="clickLogout()" class="hidden" id="barLogoutMenu">LogOut</a></li>
				<li><a href="#account" ng-click="clickAccount()" class="hidden" id="barAccountMenu">Account</a></li>
				<li><a href="#about" ng-click="clickAbout()">About</a></li>
				<li><a href="#login" ng-click="clickContact()">Contact</a></li>
			  </ul>
		</div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	
	<!-- Main Good-->
	<div id="main" class="container-fluid" ng-controller="ControllerMain" type="text/ng-template" id="home.html">
		<div class="container col-md-8 col-md-offset-2">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="form-group">
						<div class="input-group">
							<input class="form-control" type="text" placeholder="input criteria"/>
							<a href="#" ng-Click="searchItem()" class="input-group-addon btn btn-primary" >Search Now!</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					
					<ul class="list-group">
					  <li class="list-group-item list-group-item-info"> Compound classes</li>
					  <li class="list-group-item">
					  <button type="button" class="btn btn-default badge"><span class="glyphicon glyphicon-download-alt"></span></button>
						Cras justo odio
					  </li>
					  <li class="list-group-item">
					  <button type="button" class="btn btn-default badge"><span class="glyphicon glyphicon-download-alt"></span></button>
						Cras justo JZHxvZodio
					  </li>
					  <li class="list-group-item">
					  <button type="button" class="btn btn-default badge"><span class="glyphicon glyphicon-download-alt"></span></button>
						Cras askhcsjusto odio
					  </li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- Login Good -->
	<div class="container hidden col-md-4 col-md-offset-4" id="login" ng-controller="ControllerLogin" type="text/ng-template" id="login.html">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Login</h3>
			</div>
			<div class="panel-body">
				<form role="form">
				  <div class="form-group">
					<input type="text" class="form-control" placeholder="Email" ng-model="email">
				  </div>
				  <div class="form-group">
					<input type="password" class="form-control" placeholder="Password" ng-model="password">
				  </div>
				  <a  href="#" ng-click="clickLogin()" class="btn btn-info btn-block">Submit!</a>
				</form>	
			</div>
		</div>	
	</div>
	
	<!-- Contact Us Good -->
	<div class="container hidden col-md-6 col-md-offset-3" id="contactUs" ng-controller="ControllerContactUs">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Contact Us</h3>
			</div>
			<div class="panel-body">
			  <div class="form-group">
				<input type="text" class="form-control" ng-model="msgFrom" placeholder="Enter your email">
			  </div>
			  <div class="form-group">
				<input type="text" class="form-control" ng-model="msgSubject" placeholder="Subject">
			  </div>
			  <div class="form-group">
				<textarea class="form-control" ng-model="msgComment" rows="8"  placeholder="Describe your enquiry"></textarea>
			  </div>
			  <hr>
			  <a href="#" ng-click="clicksendMsg()" class="btn btn-primary btn-block">Submit</a>
			</div>
		</div>
	</div>

	<!-- About us Good -->
	<div class="col-md-8 col-md-offset-2 hidden" id="aboutUs">
		<img class="featuredImg center-block" src="img/index3.jpg">
		<div class="container col-md-12 text-justify">
			<p>This is app has being design by Carlos D.</p>
		</div>
	</div>

	<!--Result one item-->
	<div class="row col-lg-10 col-md-offset-1 hidden"  id="oneResult" ng-controller="ControllerResultOne">
		<div class="col-sm-12">
			<div class=" col-sm-4 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<img src="img/drug.png" alt="..." class="img-thumbnail">
					</div>
				</div>
			</div>

			<div class=" col-sm-6 ">
				<div class="panel panel-default">
					<div class="panel-heading">
						Name: {{desc.cName}}
					</div>
					<div class="panel-body">
						<p>Formula: {{desc.cFormula}}</p>
						<p>Mass: {{desc.cMass}}</p>
						<p>Precursor: {{desc.cPrecursor}}</p>
						<p>Frag: {{desc.cFrag}}</p>
						<p>RT: {{desc.cRT}}</p>
						<p>CAS: {{desc.cCas}}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class=" col-sm-4 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<img src="img/graph.png" alt="..." class="img-thumbnail">
					</div>
				</div>
			</div>

			<div class=" col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						Trascendals
					</div>
					<div class="panel-body">
						<table class="table table-striped table-responsive">
						  <tr class="success">
							<td>Product</td>
							<td>CE</td> 
							<td>Abund.</td>
							<td>R.Intens.</td>
						  </tr>  
						  <tr ng-repeat="items in trans">
							<td>{{items.tProduct}}</td>
							<td>{{items.tCE}}</td> 
							<td>{{items.tAbundance}}</td>
							<td>{{items.tRIInt}}</td>
						  </tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- User Good -->
	<div class="container hidden" id="userMain">
		<div class="row">
		
			<!-- Menu -->
			<div class="col-lg-3" ng-controller="ControllerAccountMenu" id="accntMenu">
				<div class="list-group">
					<a  href="#sumary" ng-click="clicksumary()" class="list-group-item">
						<h4 class="list-group-item-heading" ng-show="userinfo.permission =='admin'">Managment Summary</h4>
						<p class="list-group-item-text" ng-show="userinfo.permission =='admin'">See summary of the system.</p>
						<h4 class="list-group-item-heading" ng-show="userinfo.permission =='labOP'">Chem. Comp. Summary</h4>
						<p class="list-group-item-text" ng-show="userinfo.permission =='labOP'">See Compound Summary summary.</p>
					</a>
					<a href="#CompManag" ng-click="clickCourseDetails()"  class="list-group-item">
						<h4 class="list-group-item-heading">Compound Managment</h4>
						<p class="list-group-item-text" ng-show="userinfo.permission =='admin'">Details of compound Managment</p>
					</a>
					<a  href="#AccountDetails" ng-click="clickAccountDetails()" class="list-group-item">
						<h4 class="list-group-item-heading">Account Details</h4>
						<p class="list-group-item-text">Account Login info, user email, address, etc.</p>
					</a>
					<a href="#ManageUsers" ng-Click="clickMnanageUsers()" class="list-group-item" ng-show="userinfo.permission =='admin'">
						<h4 class="list-group-item-heading">Manage Users</h4>
						<p class="list-group-item-text">Delete and Upgrade/downgrade Users from/to admin.</p>
					</a>
				</div>
			</div>
			
			<!-- main -->
			<div class="col-lg-9"  id="userMainSum" ng-controller="ControllerAccountSummary">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Account Summary</h3>
					</div>
					<div class="panel-body">
						<ul class="nav nav-pills nav-stacked">
						  <li class="list-group-item list-group-item-success">Drugs Uploaded<span class="badge pull-right">{{accntSumary.systemUnreadMsg}}</span></li>
						  <li class="list-group-item list-group-item-success" ng-show="userinfo.permission =='admin'">Current Lab Operators<span class="badge pull-right">{{accntSumary.booksActive}}</span></li>
						  <li class="list-group-item list-group-item-success" ng-show="userinfo.permission =='admin'">Current admins<span class="badge pull-right">{{accntSumary.booksSaleCurrentSemester}}</span></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- buy -->
			<div class="col-lg-9 hidden"  id="userMainBuy" ng-controller="ControllerCourseDetail">
				<div class="panel panel-default">
					<div class="panel-heading">
						Chemical Compound
						<a href="#" class="btn btn-default  pull-right" type="button">Add</a>
					</div>
					<div class="panel-body">
						<table class="table table-responsive">
							<tr>
								<td>Compound Name</td>
								<td>Formula</td>
								<td></td>
							</tr>
							<tr ng-repeat="items in uCompund">
								<!--<td><a href="javascript: angular.element($('#searchResult')).scope().accnt_one_item(2);">{{items.itemName}}</a></td>-->
								<td>{{items.cName}}</td>
								<td>{{items.cFormula}}</td>
								<td>
									<!-- Small button group -->
									<div class="btn-group">
									  <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
										Actions <span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu" role="menu">
										<li><a href="#" ng-click="callModalSendMessage(email, 'admin', 'Item No:'+items.transactionId);">Delete</a></li>
										<li><a href="#" ng-click="callModalSendMessage(email, 'admin', 'Item No:'+items.transactionId);">Edit</a></li>
									  </ul>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<!-- User Msg -->
			<div class="col-lg-9 hidden"  id="userMainMsg"  ng-controller="ControllerAccountMsg">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Account Messages</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
						  <a href="#" ng-Click="accnt_One_msg(items.emailNo)" class="list-group-item" ng-repeat="items in msgs">
							<h4 class="list-group-item-heading">From: {{items.from}}</h4>
							<p class="list-group-item-text">Subject: {{items.subject}}</p>
						  </a>
						</div>
					</div>
				</div>
			</div>
		
			<!-- account Details -->
			<div class="col-lg-9 hidden" id="userMainDtail" ng-controller="ControllerAccountDetail">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Account Details</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							<a href="#" class="list-group-item"  ng-Click="callModalChangeOneAttribute('Change first name','Enter a new first name', 'FirstName', '')"><span class="badge">modify</span>First Name: {{accountDtl.FirstName}}</a>
							<a href="#" class="list-group-item"  ng-Click="callModalChangeOneAttribute('Change middle name','Enter a new middle name', 'MiddleName', '')"><span class="badge">modify</span>Middle Name: {{accountDtl.MiddleName}}</a>
							<a href="#" class="list-group-item"  ng-Click="callModalChangeOneAttribute('Change last name','Enter a new last name', 'LastName', '')"><span class="badge">modify</span>Last Name: {{accountDtl.LastName}}</a>
							<div class="list-group-item">Email: {{accountDtl.Email}}</div>
							<a href="#" class="list-group-item"  ng-Click="callModalChangeOneAttribute('Change address','Enter a new address', 'Address', '')"><span class="badge">modify</span>Address: {{accountDtl.Address}}</a>
							<a href="javascript: $('#mchangePassword').modal('show');" class="list-group-item" ><span class="badge">modify</span>Password: *********************</a>
						</div>
					</div>
				</div>
			</div>

			<!-- manage Search Users -->
			<div class="col-lg-9 hidden" id="userMng" ng-controller="ControllerManageUsers">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h3>Search for a user</h3> 
						</div>
						<div class="panel-body">
							<div class="form-group">
								<input type="text" class="form-control" id="AccntUEmail" placeholder="Email">
							</div>
							<hr/>
							<a href="javascript: searchOneUser($('#AccntUEmail').val())" class="btn btn-primary btn-block">Search</a>
						</div>
				</div>
			</div>
	
		</div>
	</div>
	
	<!--Result modal list-->
	<div class="modal fade" id="resultList" ng-controller="ControllerResultList">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title">Select the proper element,</h4>
		  </div>
		  <div class="modal-body">
			<div class="list-group">
			  <a href="#" class="list-group-item" ng-repeat="items in resultL" ng-Click="selectOne(items.cid)">{{items.cFormula}} : {{items.cName}}</a>
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!--Password Change modal-->
	<div class="modal fade" id="mchangePassword">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title">Change Password</h4>
		  </div>
		  <div class="modal-body">
  			  <div class="form-group">
				<input type="Password" class="form-control" id="moldpass" placeholder="Old Password">
			  </div>
  			  <div class="form-group">
				<input type="Password" class="form-control" id="mnewpass" placeholder="New Password">
			  </div>
  			  <div class="form-group">
				<input type="Password" class="form-control" id="mnewtwopass" placeholder="Re-enter New Password">
			  </div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<a href="javascript: $('#mchangePassword').modal('hide'); update_accnt_detail('', '', '', '', $('#moldpass').val(), $('#mnewpass').val(), $('#mnewtwopass').val());$('#moldpass').val('');$('#mnewpass').val('');$('#mnewtwopass').val('');" class="btn btn-primary">Submit</a>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->	
	
	<!--One attribute change modal-->
	<div class="modal fade" id="moneAttributeChange">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="mtitleOAC">change attribute</h4>
		  </div>
		  <div class="modal-body">
  			  <div class="form-group">
				<input type="text" class="form-control" id="moneAtt">
			  </div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<a href="javascript: $('#mSendMsg').modal('hide'); changeOneAttribute($('#moneAtt').val());$('#moneAtt').val('');" class="btn btn-primary">Submit</a>
		  </div>
		</div><!-- /.modal-content -->
	 </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->		
		
	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.js"></script>
    <!--<script src="http://code.angularjs.org/snapshot/angular.js"></script> //double?-->
</body>
</html>
