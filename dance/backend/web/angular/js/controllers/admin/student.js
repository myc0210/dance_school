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
    [        '$scope', '$http', 'promiseSchools', 'promiseBranches', 'ngToast','AdminStudentService',
    function ($scope,   $http,  promiseSchools, promiseBranches, ngToast, AdminStudentService) {
        $scope.page.title = 'Create New Student';
        $scope.csrf = {value: ''};
        $scope.student = {};
        $scope.branchLock = true;
        $scope.school = {};
        $scope.branch = {};
        $scope.schools = promiseSchools;
        $scope.branches = [];

        $scope.studentCreate = function() {
            this.student.schoolId = $scope.school.selected.id;
            this.student.branchId = $scope.branch.selected.id;
            var waitToast = ngToast.create({
                className: 'info',
                content: '<div class="timer-loader"></div>Processing...'
            });
            var promise = AdminStudentService.studentCreate(this.student);
            promise.then(
                function (data) {
                    ngToast.dismiss(waitToast);
                    ngToast.create({
                        className: 'success',
                        dismissOnTimeout: true,
                        content: 'Success!'
                    });
                    $scope.student = {};
                    $scope.branch = {};
                    $scope.school = {};
                    $scope.branches.length = 0;
                },
                function (reason) {
                    ngToast.dismiss(waitToast);
                    ngToast.create({
                        className: 'danger',
                        dismissOnTimeout: true,
                        content: 'System error! Please try again!'
                    });
                }
            );

        };

        $scope.checkLock = function() {
            $scope.branches.length = 0;
            promiseBranches.forEach(function(val, index, self) {
                if (val.school_id == $scope.school.selected.id) {
                    $scope.branches.push(val);
                }
            });
            $scope.branchLock = false;
        };
    }]
)
.controller('AdminStudentListController',
    [        '$scope', '$resource', '$compile', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminStudentService',
    function ($scope,   $resource,   $compile,   DTOptionsBuilder,   DTColumnBuilder,   SweetAlert, ngToast, AdminStudentService) {
        $scope.csrf = {value: ''};

        $scope.dtInstance = {};

        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            return $resource('student/student-list').query().$promise;
        })
            .withPaginationType('full_numbers')
            .withOption('createdRow', createdRow);

        $scope.dtColumns =  [
            DTColumnBuilder.newColumn('id').withTitle('ID'),
            DTColumnBuilder.newColumn('first_name').withTitle('First name'),
            DTColumnBuilder.newColumn('last_name').withTitle('Last name'),
            DTColumnBuilder.newColumn('identity_no').withTitle('NRIC/Passport No'),
            DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                .renderWith(actionsHtml)
        ];

        $scope.edit = function (id) {
            $scope.message = 'You are trying to edit the row with ID: ' + id;
            // Edit some data and call server to make changes...
            // Then reload the data so that DT is refreshed
            $scope.dtInstance.reloadData();
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
                    var promise = AdminStudentService.studentDelete(id);
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
                            $scope.serverError = reason;
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
                '   <i class="fa fa-eye"></i>' +
                '</button>&nbsp;' +
                '<button class="btn btn-danger" ng-click="delete(' + data.id + ')">' +
                '   <i class="fa fa-trash-o"></i>' +
                '</button>';
        }
    }]
)