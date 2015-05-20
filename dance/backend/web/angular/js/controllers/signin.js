'use strict';

/* Controllers */
// signin controller
app.controller('SigninFormController',
    [        '$scope', '$http', '$state', '$localStorage',
    function ($scope,   $http,   $state,   $localStorage) {

        $scope.user = {};
        $scope.csrf = {value: ''};
        $scope.authError = null;
        $scope.enable = true;

        $scope.login = function () {
            $scope.enable = true;
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
                if (response.data.accessToken && response.data.user) {
                    $localStorage.accessToken = response.data.accessToken;
                    $localStorage.user = response.data.user;
                    switch (response.data.user.role) {
                        case 'owner':
                            $state.go('owner.dashboard');
                            break;
                        case 'admin':
                            $state.go('admin.dashboard');
                            break;
                        case 'student':
                        case 'relative':
                            $state.go('student.dashboard');
                            break;
                    }
                } else {

                }
            }, function (error) {
                switch (error.status) {
                    case 403:
                        $scope.authError = 'Username/Email or Password is invalid.';
                        break;
                    default:
                        $scope.authError = 'Server Error';
                }
            });
        };
}])
;