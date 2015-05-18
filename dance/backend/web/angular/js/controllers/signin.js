'use strict';

/* Controllers */
// signin controller
app.controller('SigninFormController', ['$scope', '$http', '$state', function ($scope, $http, $state) {
    $scope.user = {};
    $scope.csrf = {value: ''};
    $scope.authError = null;
    $scope.login = function () {
        $scope.authError = null;
        console.log($scope);
        // Try to login
        $http.post('user/login',
                {
                    username: $scope.user.username,
                    password: $scope.user.password,
                    _csrf: $scope.csrf.value
                }
            )
            .then(function (response) {
                if (!response.data.user) {
                    $scope.authError = 'Username/Email or Password not right';
                } else {
                    //$state.go('app.dashboard-v1');
                    console.log('Success');
                }
            }, function (error) {
                $scope.authError = 'Server Error';
            });
    };
}])
;