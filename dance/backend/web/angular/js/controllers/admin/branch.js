'use strict';

app.controller('AdminBranchGeneralController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page = {
            title: 'School Branch'
        };
    }]
)
    .controller('AdminBranchCreateController',
    [        '$scope', '$state', '$http', 'ngToast', 'AdminBranchService', 'schools',
    function ($scope,   $state,   $http,   ngToast,   AdminBranchService, schools) {
        $scope.page.title = 'Create New Branch';
        $scope.page.buttons = [
            {
                function: function goToList() {
                    $state.go('admin.branch.list');
                },
                name: 'School Branch List'
            }
        ];

        $scope.csrf = {value: ''};
        $scope.schools = schools;
        $scope.branch = {};

        $scope.branchCreate = function() {
            var waitToast = ngToast.create({
                className: 'info',
                content: '<div class="timer-loader"></div>Processing...'
            });
            var promise = AdminBranchService.branchCreate($scope.branch);
            promise.then(
                function (data) {
                    ngToast.dismiss(waitToast);
                    ngToast.create({
                        className: 'success',
                        dismissOnTimeout: true,
                        content: 'Success!'
                    });
                    $state.transitionTo($state.current, {}, {
                        reload: true
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
    .controller('AdminBranchListController',
    [        '$scope', '$resource', '$compile', '$state', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminBranchService',
        function ($scope,   $resource,   $compile,   $state,   DTOptionsBuilder,            DTColumnBuilder,   SweetAlert,   ngToast,   AdminBranchService) {
            $scope.page.title= 'School Branch List';

            $scope.page.buttons = [
                {
                    function: function goToCreate() {
                        $state.go('admin.branch.create');
                    },
                    name: 'Create New Branch'
                }
            ];

            $scope.csrf = {value: ''};

            $scope.dtInstance = {};

            $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
                return $resource('branch/branch-list').query().$promise;
            })
                .withPaginationType('full_numbers')
                .withOption('createdRow', createdRow);

            $scope.dtColumns =  [
                DTColumnBuilder.newColumn('id').withTitle('ID'),
                DTColumnBuilder.newColumn('school_name').withTitle('School Name'),
                DTColumnBuilder.newColumn('name').withTitle('Branch Name'),
                DTColumnBuilder.newColumn('address').withTitle('Address'),
                DTColumnBuilder.newColumn('postcode').withTitle('Postcode'),
                DTColumnBuilder.newColumn('phone').withTitle('Contact'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ];

            $scope.edit = function (id) {
                $state.go('admin.branch.edit', {branchId: id});
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
                            var promise = AdminBranchService.branchDelete(id);
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
    .controller('AdminBranchEditController',
    [        '$scope', 'ngToast', '$state', 'AdminBranchService', 'branch', 'schools',
    function ($scope,   ngToast,   $state,   AdminBranchService,   branch,   schools) {
        $scope.page.title = 'Edit Branch ' + branch.name;
        $scope.page.buttons = [
            {
                function: function goToList() {
                    $state.go('admin.branch.list');
                },
                name: 'School Branch List'
            },
            {
                function: function goToList() {
                    $state.go('admin.branch.create');
                },
                name: 'Create New Branch'
            }
        ];
        $scope.branch = branch;
        $scope.schools = schools;
        $scope.csrf = {value: ''};

        $scope.branchUpdate = function () {
            var waitToast = ngToast.create({
                className: 'info',
                content: '<div class="timer-loader"></div>Processing...'
            });
            var promise = AdminBranchService.branchUpdate(branch);
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