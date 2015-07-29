'use strict';

app.controller('AdminTeacherGeneralController',
    [        '$scope', '$http',
        function ($scope,   $http) {
            $scope.page = {
                title: 'Teacher'
            };
        }]
)
.controller('AdminTeacherCreateController',
[        '$scope', '$state', '$http', 'ngToast', 'AdminTeacherService',
function ($scope,   $state,   $http,   ngToast,   AdminTeacherService) {
    $scope.page.title = 'Create New Teacher';
    $scope.csrf = {value: ''};
    $scope.teacher = {};

    $scope.teacherCreate = function() {
        var waitToast = ngToast.create({
            className: 'info',
            content: '<div class="timer-loader"></div>Processing...'
        });
        var promise = AdminTeacherService.teacherCreate($scope.teacher);
        promise.then(
            function (data) {
                ngToast.dismiss(waitToast);
                ngToast.create({
                    className: 'success',
                    dismissOnTimeout: true,
                    content: 'Success!'
                });
                $state.transitionTo($state.current, {}, {
                    reload: true,
                });
            },
            function (reason) {
                $scope.serverError = reason;
            }
        );
    }
}]
)
.controller('AdminTeacherListController',
[        '$scope', '$resource', '$compile', '$state', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminTeacherService',
function ($scope,   $resource,   $compile,   $state,   DTOptionsBuilder,            DTColumnBuilder,   SweetAlert,   ngToast,   AdminTeacherService) {
        $scope.page.title= 'Teacher List';

        $scope.csrf = {value: ''};

        $scope.dtInstance = {};

        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            return $resource('teacher/teacher-list').query().$promise;
        })
            .withPaginationType('full_numbers')
            .withOption('createdRow', createdRow);

        $scope.dtColumns =  [
            DTColumnBuilder.newColumn('id').withTitle('ID'),
            DTColumnBuilder.newColumn('first_name').withTitle('First name'),
            DTColumnBuilder.newColumn('last_name').withTitle('Last name'),
            DTColumnBuilder.newColumn('identity_no').withTitle('NRIC/Passport No'),
            DTColumnBuilder.newColumn('description').withTitle('Description'),
            DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                .renderWith(actionsHtml)
        ];

        $scope.edit = function (id) {
            $state.go('admin.teacher.edit', {teacherId: id});
        }

        $scope.delete = function (id) {
            SweetAlert.swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel plx!"
                },
                function(isConfirm){
                    if (isConfirm) {
                        var waitToast = ngToast.create({
                            className: 'info',
                            content: '<div class="timer-loader"></div>Processing...'
                        });
                        var promise = AdminTeacherService.teacherDelete(id);
                        promise.then(
                            function (data) {
                                ngToast.dismiss(waitToast);
                                ngToast.create({
                                    className: 'success',
                                    dismissOnTimeout: true,
                                    content: 'Success!'
                                });
                                $scope.dtInstance.reloadData();
                            },
                            function (reason) {
                                ngToast.dismiss(waitToast);
                                ngToast.create({
                                    className: 'danger',
                                    dismissOnTimeout: true,
                                    content: 'Error: ' + reason + '!'
                                });
                            }
                        );
                    }
                });
        }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml(data, type, full, meta) {
            return '<button class="btn btn-primary" ng-click="edit(' + data.id + ')">' +
                '   <i class="fa fa-pencil-square-o"></i>' +
                '</button>&nbsp;' +
                '<button class="btn btn-danger" ng-click="delete(' + data.id + ')">' +
                '   <i class="fa fa-trash-o"></i>' +
                '</button>';
        }
    }]
)
.controller('AdminTeacherEditController',
[        '$scope', 'ngToast', 'AdminTeacherService', 'teacher',
function ($scope,   ngToast,   AdminTeacherService,   teacher) {
    $scope.teacher = teacher;

    $scope.csrf = {value: ''};

    $scope.teacherUpdate = function () {
        var waitToast = ngToast.create({
            className: 'info',
            content: '<div class="timer-loader"></div>Processing...'
        });
        var promise = AdminTeacherService.teacherUpdate(teacher);
        promise.then(
            function (data) {
                ngToast.dismiss(waitToast);
                ngToast.create({
                    className: 'success',
                    dismissOnTimeout: true,
                    content: 'Success!'
                });
                $scope.dtInstance.reloadData();
            },
            function (reason) {
                ngToast.dismiss(waitToast);
                ngToast.create({
                    className: 'danger',
                    dismissOnTimeout: true,
                    content: 'Error: ' + reason + '!'
                });
            }
        );
    };
}]
);