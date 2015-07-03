'use strict';

app.controller('AdminStudentGeneralController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page = {
            title: 'Student'
        };
    }]
)
.controller('AdminStudentCreateController',
    [        '$scope', '$http', 'AdminStudentService',
    function ($scope,   $http,   AdminStudentService) {
        $scope.page.title = 'Create New Student';
        $scope.csrf = {value: ''};
        $scope.student = {};

        $scope.studentCreate = function() {
            AdminStudentService.studentCreate(this.student);
        }
    }]
)