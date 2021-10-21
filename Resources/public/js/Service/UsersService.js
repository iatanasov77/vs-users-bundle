
define(['ia/application', 'ia/services/Base'], function(app) {
    app.factory('UsersService', function (BaseService, $sce) {
        var UsersService = Object.create(BaseService);
        UsersService.baseUrl = 'service/users';

        return UsersService;
    });
});
