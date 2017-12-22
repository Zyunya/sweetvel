var admin_home    = angular.module('admin_home',[]);
var personal_page = angular.module('personal_page',[]);

admin_home.controller('admin_homectr',function($scope,$http){
 $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

 $scope.login_in = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'autoriz',log : $id('login').value,pass : $id('password').value})
}).then(function(response) {
   if(response.data.status == 1){
       document.location.href = 'admin/personalpage.html';
   }
   else{
       $scope.login_outp = response.data.text;  
   }
}); 

 }


})


personal_page.controller('personal_pagectr',function($scope,$http){
 $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

 $scope.get_users = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'get_users'})
}).then(function(response) {
//console.log(response.data);
$scope.user_list    = response.data.record;
$scope.user_counter = response.data.quantity;
  }); 
 }
 $scope.get_visitors = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'get_visitors'})
}).then(function(response) {
//console.log(response.data);
$scope.visitors_list    = response.data.record;

  }); 
 }
  $scope.get_callback = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'get_callback'})
}).then(function(response) {
console.log(response.data);
$scope.callback    = response.data.record;

  }); 
 }
  $scope.delete_profile = function(id){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'delete_profile_admin',id : id})
}).then(function(response) {
//console.log(response.data);
  }); 
 }
  $scope.log_out = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'log_out'})
}).then(function(response) {
//console.log(response.data);
if(response.data == "1"){
     document.location.href = 'admin/admin.html';
}
else{
    return false;
    }
  }); 
 }
 $scope.check_session = function(){
 $http({method : 'POST',url : '/ajax/ajax.php' ,data : $.param({ajax : 'check_session'})
}).then(function(response) {
//console.log(response.data);
if(response.data == "0"){
    $scope.authorized = false;
    document.location.href = 'admin/admin.html';
}
else{
    $scope.authorized = true;
    }
  }); 
 }
$scope.get_users();
$scope.get_visitors();
$scope.check_session();
$scope.get_callback();

})