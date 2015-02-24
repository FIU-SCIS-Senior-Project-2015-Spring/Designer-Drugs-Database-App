var sys = {q:[], r:[], ctrl:null,user:null,navbarScope:null,passVariable:null};



////////////////////////////ANGULAR JS///////////////////////////////
var myapp = angular.module('mainApp', ['angularFileUpload','ngRoute']);


/* this is the controller of the navbar.
* the navbar scope is on the global variable to be able to modify the 
* userinfo variable and automatically reflect changes on the navBar
*(changes like hiding and showing buttons). in this case when the variable
* userinfo is changed through sys.navbarScope then the digest refreshes the 
* page automatically because there has been a change to the scope.
*/
myapp.controller('ControllerNavbar', function($scope, $location) {
	$scope.userinfo = sys.user;
	sys.navbarScope = $scope;
	
	$scope.clickLogout= function(){
	$.ajax({
		type: "POST",
		url: "request/authModuleClass.php",
		data: {section: "logOut"}
		})
		.done(function( msg ) {
			sys.user = null;
			$scope.userinfo = sys.user;
			$location.path("/home");
		});
		
/*		
		$http.post("request/authModuleClass.php",{section: "logOut"})
		.success(function(json) {
			sys.user = null;
			$scope.userinfo = sys.user;
			$location.path("/home");
		});		
*/	}
});                                    
	
myapp.config(['$routeProvider',function($routeProvider) {
	$routeProvider
		.when('/:page', {
			templateUrl : function(route){
			sys.q = [];
			return "pages/"+route.page+".html";
			},
			controller  : 'MyController'
		})
		.otherwise({
			redirectTo : '/home'
		});
}]);

myapp.controller('MyController', function($scope, $http, $location) {
	$scope.userinfo = sys.user;
    $scope.r = [];
    var len_q = sys.q.length;
    for (var i = 0; i < len_q; i++) {
        $http(sys.q[i]).success(function(data, status, headers, config) {
            $.each(sys.q, function(index, obj) {
                if (config.url == obj.url) {
                    //BEGIN: Hash Map
                    if (obj.hashmap) {
                        // Hash Map Debug
                        if (obj.hashmapdebug) console.log("Hash Map Input .q["+index+"]", data);
                        // Removing leading and trailing slashes
                        obj.hashmap = obj.hashmap.replace(/^\/+|\/+$/g, '');
                        var p = obj.hashmap.split("/"); // Path array
                        var len_p = p.length; // Path deep
                        $scope.r[index] = []; // Init
                        //BEGIN: Do the magic
                        for (var j = 0; j < len_p; j++) {
                            var last = (j == len_p - 1); // Last element
                            if (data[p[j]]) { // Object child
                                if (last) $scope.r[index][data[p[j]]] = data;
                                else data = data[p[j]]; // Reducing hierarchy
                            } else if (angular.isDefined(data[0]) && data[0][p[j]]) { // Array child
                                if (last) angular.forEach(data, function(v) { $scope.r[index][v[p[j]]] = v; });
                                else data = data[0][p[j]]; // Reducing hierarchy
                            } else break;
                        }
                        //END: Do the magic
                        // Hash Map Debug
                        if (obj.hashmapdebug) console.log("Hash Map Output r["+index+"]", $scope.r[index]);
                    //END: Hash Map
                    } else $scope.r[index] = data; // No hashmap
                    return false; // Break the $.each()
                }
            });
        });
    }
	if(angular.isFunction(sys.ctrl)) sys.ctrl($scope,$http);
	sys.ctrl = null;
	
	$scope.go = function ( path ) {
		$location.path( path );
	};
});


//old controllers	
myapp.controller('ControllerMain', function($scope) {
	$scope.searchItem= function(){
		var $scope = angular.element($("#resultList")).scope();
		$scope.showList();
		$('#resultList').modal('show');
	}
});	

/*
myapp.controller('ControllerLogin', function($scope) {
	$scope.userinfo = new Array();
    $scope.email="";
	$scope.password="";

	$scope.Login= function(){
	$.ajax({
	  type: "POST",
	  url: "request/accntLogin.php",
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
		mydesignerdrug.userinfo = msg;
		
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
*/
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
	  url: "request/searchResultList.php",
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
	  url: "request/searchResultOneDesc.php",
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
	  url: "request/searchResultOneTrans.php",
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


myapp.controller('ControllerAccountMenu', function($scope) {
	//TODO this need to change and come from the login result
	$scope.userinfo = new Array();
	$scope.userinfo.permission ='none';
	
	$scope.clicksumary= function(){
		show_Page(7);
	}

	$scope.clickCourseDetails= function(){
		show_Page(10);
	}
	
	$scope.clickCourseManagment= function(){
		show_Page(11);
	}	

	$scope.clickAccountDetails= function(){
		show_Page(12);
	}	
	
	$scope.clickMessages= function(){
		show_Page(13);
	}

	$scope.clickMnanageUsers = function(){
		show_Page(20);
	}
});

myapp.controller('ControllerAccountSummary', function($scope) {
	$scope.userinfo = new Array();
	$scope.userinfo.permission = mydesignerdrug.userinfo.permission;

	
	$scope.refreshContent= function(){
		var $scope2 = angular.element($("#login")).scope();
		$scope.userinfo = $scope2.getUserInfo();
	}

});

myapp.controller('ControllerCourseDetail', function($scope) {
});

myapp.controller('ControllerAccountMsg', function($scope) {
});

myapp.controller('ControllerAccountDetail', function($scope) {
});

myapp.controller('ControllerManageUsers', function($scope) {
});
*/



