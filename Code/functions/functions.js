var sys = {q:[], r:[]};

function show_Page(index) {
	$("#main").hide().removeClass('hide');
	$("#login").hide().removeClass('hidden');
	$("#newUser").hide().removeClass('hidden');
	$("#contactUs").hide().removeClass('hidden');
	$("#aboutUs").hide().removeClass('hidden');
	$("#example").hide().removeClass('hidden');
	$("#findMore").hide().removeClass('hidden');
	$("#userMain").hide().removeClass('hidden');
	$("#userMainSum").hide().removeClass('hidden');
	$("#userMainBuy").hide().removeClass('hidden');
	$("#userMainSell").hide().removeClass('hidden');
	$("#userMainMsg").hide().removeClass('hidden');
	$("#userMainDtail").hide().removeClass('hidden');
	$("#userOneMsg").hide().removeClass('hidden');
	$("#searchResult").hide().removeClass('hidden');
	$("#itemSale").hide().removeClass('hidden');
	$("#newItemSale").hide().removeClass('hidden');
	$("#shoppingCart").hide().removeClass('hidden');
	$("#checkout").hide().removeClass('hidden');
	$("#userMng").hide().removeClass('hidden');
	$("#userOneMng").hide().removeClass('hidden');
	$("#productSrch").hide().removeClass('hidden');
	$("#oneResult").hide().removeClass('hidden');
	
	
	
	switch(index){
		case 0: $("#main").show();
				break;
		case 1: $("#login").show();
				break;
		case 2: $("#newUser").show();
				break;
		case 3: $("#contactUs").show();
				break;
		case 4: $("#aboutUs").show();
				break;
		case 5: $("#example").show();
				break;
		case 6: $("#findMore").show();
				break;
		case 7: $("#userMain").show();
				$("#barLoginMenu").hide().removeClass('hidden');
				$("#barLogoutMenu").removeClass('hidden');
				$("#barAccountMenu").removeClass('hidden');
				$("#barShoppingMenu").removeClass('hidden');
				$("#barLogoutMenu").show();
				$("#barAccountMenu").show();
				$("#barShoppingMenu").show();
				$("#userMainSum").show();
				break;
		case 8: $("#main").show();
				$("#barLoginMenu").show().removeClass('hidden');
				$("#barLogoutMenu").hide().removeClass('hidden');
				$("#barAccountMenu").hide().removeClass('hidden');
				$("#barShoppingMenu").hide().removeClass('hidden');
				break;
		case 9: $("#userMain").show();
				$("#userMainSum").show();
				break;
		case 10:$("#userMain").show();
				$("#userMainBuy").show();
				break;
		case 11:$("#userMain").show();
				$("#userMainSell").show();
				break;
		case 12:$("#userMain").show();
				$("#userMainDtail").show();
				break;
		case 13:$("#userMain").show();
				$("#userMainMsg").show();
				break;
		case 14:$("#userMain").show();
				$("#userOneMsg").show();
				break;
		case 15:$("#itemSale").show();
				break;
		case 16:$("#searchResult").show();
				break;		
		case 17:$("#newItemSale").show();
				break;		
		case 18:$("#shoppingCart").show();
				break;
		case 19:$("#checkout").show();
				break;
		case 20:$("#userMain").show();
				$("#userMng").show();
				break;
		case 21:$("#userMain").show();
				$("#userOneMng").show();
				break;
		case 22:$("#userMain").show();
				$("#productSrch").show();
				break;		
		case 23:$("#oneResult").show();
				break;		

	}
}
////////////////////////////ANGULAR JS///////////////////////////////
var myapp = angular.module('mainApp', ['angularFileUpload','ngRoute']);

myapp.controller('ControllerNavbar', function($scope) {

	$scope.clickLogin= function(){
		show_Page(1);
	}

	$scope.clickLogout= function(){
		show_Page(8);
	}	
	
	$scope.clickHome= function(){
		show_Page(0);
	}		
	
	$scope.clickAccount= function(){
		show_Page(7);
	}

	$scope.clickAbout= function(){
		show_Page(4);
	}	
	
	$scope.clickContact= function(){
		show_Page(3);
	}	
	
	
});

myapp.controller('ControllerMain', function($scope) {
	$scope.searchItem= function(){
		var $scope = angular.element($("#resultList")).scope();
		$scope.showList();
		$('#resultList').modal('show');
	}
});	

myapp.controller('ControllerLogin', function($scope) {
	$scope.userinfo = new Array();
    $scope.email="";
	$scope.password="";

	$scope.Login= function(){
	$.ajax({
	  type: "POST",
	  url: "database/accntLogin.php",
	  data: { 
	  email: $scope.email,
	  password: $scope.password}
	})
	  .done(function( msg ) {
		//console.log(msg);
		if((msg == null) || (!(typeof msg==='object'))){
			$('#titleWSignUPInfo').text("Login Un-SuccessFul");		
			$('#tWSignUPInfo').text(msg);
			$('#mWSignUPInfo').modal('show');
		}else{
		$scope.userinfo = msg; 
		//mydesignerdrug.userinfo = msg;
		
		var $scope2 = angular.element($("#accntMenu")).scope();
	   		$scope2.$apply(function(){
				$scope2.userinfo = msg; 
		});
		var $scope2 = angular.element($("#userMainSum")).scope();
		$scope2.$apply($scope2.refreshContent());
		
		show_Page(7);
	  }});
	}    

	$scope.clickLogin= function(){
	 $scope.Login();
	 $scope.password = "";
	}
	
	$scope.getUserInfo= function(){
		return $scope.userinfo;
	}
});

myapp.controller('ControllerAboutUs', function($scope) {
	console.log("it is in the controller");
});

myapp.controller('ControllerContactUs', function($scope) {
    $scope.msgFrom="";
	$scope.msgSubject="";
	$scope.msgComment="";
	
	$scope.sendMsg= function(){
		alert($scope.msgFrom);
	}    

	$scope.clicksendMsg= function(){
		$scope.sendMsg();
		$scope.msgFrom="";
		$scope.msgSubject="";
		$scope.msgComment="";
	}

});

myapp.controller('ControllerResultList', function($scope) {
	$scope.resultL = new Array();
	
	$scope.showList= function(){
	$.ajax({
	  type: "POST",
	  url: "database/searchResultList.php",
	  async: false,
	  data: {}
	})
	  .done(function( msg ) {
			$scope.resultL = msg;
		});
	}

	$scope.selectOne= function(number){
		//console.log(number);
		var $scope = angular.element($("#oneResult")).scope();
		$scope.showDescription();
		$scope.showTransition();		
		show_Page(23);
		$('#resultList').modal('hide');
	}
});

myapp.controller('ControllerResultOne', function($scope) {
	$scope.desc = new Array();
	$scope.trans = new Array();	
	
	$scope.showDescription= function(cid){
	  $.ajax({
	  type: "POST",
	  url: "database/searchResultOneDesc.php",
	  async: false,
	  data: {Cid: cid}
	})
	  .done(function( msg ) {
			$scope.desc = msg;
		});
	}
	
	$scope.showTransition= function(cid){
	$.ajax({
	  type: "POST",
	  url: "database/searchResultOneTrans.php",
	  async: false,
	  data: {Cid: cid}
	})
	  .done(function( msg ) {
			$scope.trans = msg;
		});
	}
});

/*
myapp.controller('ControllerNewUser', function($scope) {
    $scope.fName="";
	$scope.mName="";
	$scope.lName="";
	$scope.schoolName="";
	$scope.email="";
	$scope.password1="";
	$scope.password2="";

	
	$scope.CreateUser= function(){
		alert($scope.email);
	}    

	$scope.clickCreateNewUser= function(){
		$scope.CreateUser();
		$scope.fName="";
		$scope.mName="";
		$scope.lName="";
		$scope.schoolName="";
		$scope.email="";
		$scope.password1="";
		$scope.password2="";	
	}

});
*/

myapp.controller('ControllerAccountMenu', function($scope) {
	//TODO this need to change and come from the login result
	$scope.userinfo = new Array();
	$scope.userinfo.permission ='none';
	
	$scope.clicksumary= function(){
		console.log("here1");
		show_Page(7);
	}

	$scope.clickCourseDetails= function(){
		console.log("here2");
		var $scope2 = angular.element($("#userMainBuy")).scope();
		//$scope2.$apply($scope2.getResultList());
		$scope2.getResultList(); 
		show_Page(10);
	}
	
	$scope.clickCourseManagment= function(){
		console.log("here3");

		show_Page(11);
	}	

	$scope.clickAccountDetails= function(){
		console.log("here4");
		show_Page(12);
	}	
	
	$scope.clickMessages= function(){
		console.log("here5");
		show_Page(13);
	}

	$scope.clickMnanageUsers = function(){
		console.log("here6");
		show_Page(20);
	}
});

myapp.controller('ControllerAccountSummary', function($scope) {
	$scope.userinfo = new Array();
	//$scope.userinfo.permission = mydesignerdrug.userinfo.permission;

	
	$scope.refreshContent= function(){
		var $scope2 = angular.element($("#login")).scope();
		$scope.userinfo = $scope2.getUserInfo();
	}

});

myapp.controller('ControllerCourseDetail', function($scope) {
	$scope.uCompund = new Array();
	
	$scope.getResultList= function(){
	$.ajax({
	  type: "POST",
	  url: "database/searchResultList.php",
	  async: false,
	  data: {}
	})
	  .done(function( msg ) {
			$scope.uCompund = msg;
		});
	}

});

myapp.controller('ControllerAccountMsg', function($scope) {
});

myapp.controller('ControllerAccountDetail', function($scope) {
});

myapp.controller('ControllerManageUsers', function($scope) {
});






