define(['ia/application'], function(app) {
    app.controller('EditUserController', 
        ['$rootScope', '$scope', '$location', '$routeParams', 'UsersService', 
    
        function($rootScope, $scope, $location, $routeParams, service) {

            var id = $routeParams.id ? parseInt($routeParams.id, 10) : 0;
            var promise = service.getItem(id);

            promise.then(function(data) {
                $scope.item = data;  
            }, function(data) {
              // error
            });

           /*
            * Save a contact
            */
            $scope.save = function () {
                service.save($scope.item).then(function(response) {
                    //success
                    $rootScope.$broadcast("updateSuccess");
                    $scope.item = null;
                    $location.path('/users');
                }, function(response) {
                  // error
                });
            };
        }
        
    ]);
});
