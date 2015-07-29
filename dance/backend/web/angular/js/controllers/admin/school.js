'use strict';

app.controller('AdminSchoolGeneralController',
    [        '$scope', '$http',
        function ($scope,   $http) {
            $scope.page = {
                title: 'School Branch'
            };
        }]
)
    .controller('AdminSchoolCreateController',
    [        '$scope', '$state', '$http', 'ngToast', 'AdminSchoolService',
        function ($scope,   $state,   $http,   ngToast,   AdminSchoolService) {
            $scope.page.title = 'Create New School';
            $scope.page.buttons = [
                {
                    function: function goToList() {
                        $state.go('admin.school.list');
                    },
                    name: 'School List'
                }
            ];

            $scope.csrf = {value: ''};
            $scope.school = {
                name: '',
                display: 0
            };

            $scope.schoolCreate = function() {
                var waitToast = ngToast.create({
                    className: 'info',
                    content: '<div class="timer-loader"></div>Processing...'
                });
                var promise = AdminSchoolService.schoolCreate($scope.school);
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
                    function (data) {
                        ngToast.dismiss(waitToast);
                        ngToast.create({
                            className: 'danger',
                            dismissOnTimeout: true,
                            content: 'System Error: ' + data.error
                        });
                    }
                );
            }
        }]
)
    .controller('AdminSchoolListController',
    [        '$scope', '$resource', '$compile', '$state', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminSchoolService',
        function ($scope,   $resource,   $compile,   $state,   DTOptionsBuilder,            DTColumnBuilder,   SweetAlert,   ngToast,   AdminSchoolService) {
            $scope.page.title= 'School List';

            $scope.page.buttons = [
                {
                    function: function goToCreate() {
                        $state.go('admin.school.create');
                    },
                    name: 'Create New School'
                }
            ];

            $scope.csrf = {value: ''};

            $scope.dtInstance = {};

            $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
                return $resource('school/school-list').query().$promise;
            })
                .withPaginationType('full_numbers')
                .withOption('createdRow', createdRow);

            $scope.dtColumns =  [
                DTColumnBuilder.newColumn('id').withTitle('ID'),
                DTColumnBuilder.newColumn('school_name').withTitle('School Name'),
                DTColumnBuilder.newColumn('shopping_cart_display').withTitle('Display on shopping cart'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ];

            $scope.edit = function (id) {
                $state.go('admin.school.edit', {schoolId: id});
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
                            var promise = AdminSchoolService.schoolDelete(id);
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
    .controller('AdminSchoolEditController',
    [        '$scope', 'ngToast', 'AdminSchoolService', 'school',
        function ($scope,   ngToast,   AdminSchoolService,   school) {
            $scope.school = school;

            $scope.csrf = {value: ''};

            $scope.schoolUpdate = function () {
                var waitToast = ngToast.create({
                    className: 'info',
                    content: '<div class="timer-loader"></div>Processing...'
                });
                var promise = AdminSchoolService.schoolUpdate(school);
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