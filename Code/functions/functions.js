 var sys = {q:[], r:[], ctrl:null,user:null,navbarScope:[],passVariable:null};

$(document).on('click','.navbar-collapse.in',function(e) {

    if( $(e.target).is('a') && ( $(e.target).attr('class') != 'dropdown-toggle' ) ) {
        $(this).collapse('hide');
    }

});

////////////////////////////ANGULAR JS///////////////////////////////
var myapp = angular.module('mainApp', ['angularFileUpload','ngRoute']);


/* this is the controller of the navbar.
* the navbar scope is on the global variable to be able to modify the 
* userinfo variable and automatically reflect changes on the navBar
*(changes like hiding and showing buttons). in this case when the variable
* userinfo is changed through sys.navbarScope then the digest refreshes the 
* page automatically because there has been a change to the scope.
*/
myapp.controller('ControllerNavbar', function($scope, $location, $http) {
	$scope.userinfo = sys.user;
	sys.navbarScope = $scope;
	
	$scope.clickLogout= function(){
		$http.post("request/authModuleCall.php",
		{section:"logOut"})
		.success(function(msg) {
			sys.user = null;
			$scope.userinfo = sys.user;
			$location.path("/home");
		});
	}
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
		.when('/:page/:itemId', {
			templateUrl : function(route){
			sys.q = [];
			sys.params = route;
			return "pages/"+route.page+".html";
			},
			controller  : 'MyController'
		})
		.otherwise({
			redirectTo : '/home'
		});
}]);

myapp.directive('img', function () {
    return {
        restrict: 'E',        
        link: function (scope, element, attrs) {     
            // show an image-missing image
            element.error(function () {
                var w = element.width();
                var h = element.height();
                // using 20 here because it seems even a missing image will have ~18px width 
                // after this error function has been called
                if (w <= 20) { w = 100; }
                if (h <= 20) { h = 100; }
                var url = 'img/NoPic.jpg';
                element.prop('src', url);
                element.css('border', 'double 3px #cccccc');
            });
        }
    }
});

myapp.controller('MyController', function($scope, $http, $location, $timeout) {
	$scope.timeout = $timeout;
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

	$scope.go = function ( path ) {
		$location.path( path );
	};	

	$scope.checkUserData = function () {
		if(sys.user == null) {
			$http.post("request/authModuleCall.php",
			{section:"checkUserLoggedIn"})
			.success(function(json) {
				if((json["Code"] > 2000 && json["Code"] < 3000) ||json["Code"] == 1005) $scope.go("/home");
				else if(json["Code"] == 2000){
					sys.user = json["loggedIn"];
					$scope.userinfo = sys.user;
					sys.navbarScope.userinfo = sys.user;
				}else{
					$('#mMessgTitle').text("Access Denied.");		
					$('#mMessgText').text("You must loggin to access this page.");
					$('#mMessg').modal('show');	
					$scope.go("/home");
				}
			});
		}
	};	
	
	if(angular.isFunction(sys.ctrl)) sys.ctrl($scope,$http);
	sys.ctrl = null;

});