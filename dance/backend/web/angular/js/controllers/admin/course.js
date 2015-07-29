'use strict';

app.controller('AdminCourseGeneralController',
    [        '$scope', '$http',
        function ($scope,   $http) {
            $scope.page = {
                title: ''
            };
        }]
)
    .controller('AdminCourseListController',
    [        '$scope', '$resource', '$compile', '$state', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminCourseService',
        function ($scope,   $resource,   $compile,   $state,   DTOptionsBuilder,       DTColumnBuilder,   SweetAlert,   ngToast,   AdminCourseService) {
            $scope.page.title = 'Course List';
            $scope.page.buttons = [
                {
                    function: function goToCreate() {
                        $state.go('admin.course.create');
                    },
                    name: 'Create New Course'
                }
            ];

            $scope.dtInstance = {};

            $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
                return $resource('course/course-list').query().$promise;
            })
                .withPaginationType('full_numbers')
                .withOption('createdRow', createdRow);

            $scope.dtColumns =  [
                DTColumnBuilder.newColumn('name').withTitle('Course Name'),
                DTColumnBuilder.newColumn('type_name').withTitle('Course Type'),
                DTColumnBuilder.newColumn('age_group').withTitle('Age Group'),
                DTColumnBuilder.newColumn('level').withTitle('Level'),
                DTColumnBuilder.newColumn('price').withTitle('Price'),
                DTColumnBuilder.newColumn('mall_price').withTitle('Mall Price'),
                DTColumnBuilder.newColumn('code').withTitle('Course Code'),
                DTColumnBuilder.newColumn('branch_name').withTitle('Branch'),
                DTColumnBuilder.newColumn('teacher_name').withTitle('Teacher'),
                DTColumnBuilder.newColumn('start_date').withTitle('Start Date'),
                DTColumnBuilder.newColumn('end_date').withTitle('End Date'),
                DTColumnBuilder.newColumn('lessons').withTitle('Less ons'),
                DTColumnBuilder.newColumn('capacity').withTitle('Capa city'),
                DTColumnBuilder.newColumn('time').withTitle('Time'),
                DTColumnBuilder.newColumn('duration').withTitle('Dura tion'),
                DTColumnBuilder.newColumn('enrolled').withTitle('Enrolled Student'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ];

            $scope.edit = function (id) {
                $state.go('admin.course.edit', {courseId: id});
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
                            var promise = AdminCourseService.variationCourseDelete(id);
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
    .controller('AdminCourseCreateController',
    [        '$scope', '$http', '$state', '$localStorage', 'ngToast', 'AdminCourseService', 'preloadTypes', 'preloadBranches', 'preloadTeachers', '$log',
        function ($scope,   $http,   $state,   $localStorage,   ngToast,   AdminCourseService,   preloadTypes, preloadBranches, preloadTeachers, $log) {
            var variationCourseTemplate = {
                code: '',
                selectedBranch: {},
                selectedTeacher: {},
                startDatePicker: {},
                startDate: '',
                endDatePicker: {},
                endDate: '',
                lessons: '',
                capacity: '',
                times: [{ week: 0 }],
                duration: ''
            };

            $scope.page.title = 'Add New Course';
            $scope.csrf = {value: ''};
            $scope.attachedImages = [];
            $scope.dateOptions = {
                dateFormat: 'yy',
                startingDay: 1,
                class: 'datepicker'
            };

            var today = new Date();
            $scope.initDate = today.toLocaleDateString();
            $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd/MM/yyyy', 'shortDate'];
            $scope.format = $scope.formats[2];

            $scope.open = function($event, func, index) {
                $event.preventDefault();
                $event.stopPropagation();

                if (func == 'start') {
                    $scope.course.variationCourses[index].startDatePicker.opened = true;
                } else {
                    $scope.course.variationCourses[index].endDatePicker.opened = true;
                }
            };

            $scope.addStartTime = function(index) {
                $scope.course.variationCourses[index].times.push({ week: 0 });
            };

            $scope.init = function () {
                var variationCourse = {},
                    courseCache = $localStorage.adminCourseCache,
                    attachedImages = [];

                if (courseCache) {
                    var attachedImages = $localStorage.attachedImages;
                }


                $scope.types = preloadTypes.types;

                $scope.branches = [];
                angular.forEach(preloadBranches, function(val, index, self) {
                    var tmp = {
                        id: val.id,
                        name: val.name
                    };

                    $scope.branches.push(tmp);
                });

                $scope.teachers = [];
                angular.forEach(preloadTeachers, function(val, index, self) {
                    var tmp = {
                        id: val.id,
                        name: val.first_name + val.last_name
                    };

                    $scope.teachers.push(tmp);
                });

                if (courseCache) {
                    $scope.course = courseCache;
                } else {
                    $scope.course = {
                        name: '',
                        ageGroup: '',
                        type: '',
                        level: '',
                        price: '',
                        mallPrice: '',
                        variationCourses: [
                            variationCourseTemplate,
                        ],
                        description: ''
                    };
                }



                if (attachedImages) {
                    $scope.course.attachedImages = attachedImages;
                }

                console.log($scope);
            };

            $scope.addImages = function () {
                $localStorage.adminCourseCache = $scope.course;
                $state.go('admin.media-manager', {select: true});
            };

            $scope.addTypes = function () {
                $localStorage.adminCourseCache = $scope.course;
                $state.go('admin.course.type')
            };

            $scope.duplicate = function (variationCourseSrc) {
                var variationCourse = {};
                angular.copy(variationCourseSrc, variationCourse);
                $scope.course.variationCourses.push(variationCourse);
            };

            $scope.deleteVariationCourse = function (index) {
                if ($scope.course.variationCourses.length > 1) {
                    $scope.course.variationCourses.splice(index, 1);
                }
            };

            $scope.newVariationCourse = function () {
                var variationCourse = {};
                angular.copy(variationCourseTemplate, variationCourse);
                $scope.course.variationCourses.push(variationCourse);
            };

            $scope.saveCourse = function () {
                var waitToast = ngToast.create({
                    className: 'info',
                    content: '<div class="timer-loader"></div>Processing...'
                });
                var promise = AdminCourseService.courseCreate($scope.course);
                promise.then(
                    function (data) {
                        ngToast.dismiss(waitToast);
                        ngToast.create({
                            className: 'success',
                            dismissOnTimeout: true,
                            content: 'Success!'
                        });

                        delete $localStorage.adminCourseCache;
                        delete $localStorage.attachedImages;
                        delete $localStorage.defaultImageIndex;

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
            };

            $scope.thumbnailSetDefault = function (index) {
                var defaultImage = $scope.course.attachedImages[index];

                angular.forEach($scope.course.attachedImages, function (value, key, object) {
                    if (value.default == true) {
                        object[key].default = false;
                    }
                });

                defaultImage.default = true;
                $scope.course.showcaseDetail.image = defaultImage;

                $localStorage.defaultImageIndex = index;
            };

            $scope.thumbnailDelete = function (index) {
                var attachedImages = $scope.course.attachedImages;

                if (attachedImages[index].default == true) {
                    attachedImages[0].default = true;
                }

                attachedImages.splice(index, 1);

                if (attachedImages.length == 0) {
                    delete $localStorage.defaultImageIndex;
                } else {
                    attachedImages[0].default = true;
                    $localStorage.defaultImageIndex = 0;
                }

            };

            $scope.init();
        }]
)
    .controller('AdminCourseUpdateController',
    [   '$scope', '$state', '$http', '$localStorage', 'ngToast', 'AdminCourseService', 'preloadCourse', 'preloadTypes', 'preloadBranches', 'preloadTeachers', '$log',
        function($scope, $state, $http, $localStorage, ngToast, AdminCourseService, preloadCourse, preloadTypes, preloadBranches, preloadTeachers, $log) {
            var variationCourseTemplate = {
                code: '',
                selectedBranch: '',
                selectedTeacher: '',
                startDate: '',
                endDate: '',
                lessons: '',
                capacity: '',
                times: [],
                duration: ''
            };
            var today = new Date();

            $scope.page.title = 'Edit Course';
            $scope.csrf = {value: ''};
            $scope.attachedImages = [];
            $scope.dateOptions = {
                dateFormat: 'yy',
                startingDay: 1,
                class: 'datepicker'
            };
            $scope.initDate = today.toLocaleDateString();
            $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd/MM/yyyy', 'shortDate'];
            $scope.format = $scope.formats[2];

            $scope.open = function($event, func, index) {
                $event.preventDefault();
                $event.stopPropagation();

                if (func == 'start') {
                    $scope.course.variationCourses[index].startDatePicker.opened = true;
                } else {
                    $scope.course.variationCourses[index].endDatePicker.opened = true;
                }
            };

            $scope.addStartTime = function(index) {
                $scope.course.variationCourses[index].times.push({ week: 0 });
            };

            var startDate = preloadCourse.startDate == '0000-00-00' ? null : preloadCourse.start_date;

            if (startDate) {
                var year = parseInt(startDate.substring(0, 4));
                var month = parseInt(startDate.substring(5, 7)) - 1;
                var day = parseInt(startDate.substring(8, 10));
                startDate = new Date(year, month, day);
            }

            var endDate = preloadCourse.endDate == '0000-00-00' ? null : preloadCourse.end_date;

            if (endDate) {
                var year = parseInt(endDate.substring(0, 4));
                var month = parseInt(endDate.substring(5, 7)) - 1;
                var day = parseInt(endDate.substring(8, 10));
                endDate = new Date(year, month, day);
            }

            $scope.init = function () {
                var variationCourses = [{
                        id: preloadCourse.id,
                        code: preloadCourse.code,
                        startDate: startDate,
                        endDate: endDate,
                        lessons: preloadCourse.lessons,
                        capacity: preloadCourse.capacity,
                        times: JSON.parse(preloadCourse.start_time),
                        duration: preloadCourse.duration,
                        startDatePicker: {},
                        endDatePicker: {}
                    }],
                    course = {
                        id: preloadCourse.course_id,
                        name: preloadCourse.course_name,
                        minAge: preloadCourse.age_min,
                        maxAge: preloadCourse.age_max,
                        level: preloadCourse.level,
                        price: preloadCourse.course_fee,
                        mallPrice: preloadCourse.mall_course_fee,
                        description: preloadCourse.description,
                        variationCourses: variationCourses
                    },
                    courseEditCache = $localStorage.courseEditCache,
                    attachedImages = JSON.parse(preloadCourse.gallery_detail);

                $scope.types = preloadTypes.types;
                $scope.branches = preloadBranches;
                $scope.teachers = [];

                angular.forEach(preloadTypes.types, function(val, index, self) {
                    if (val.id == preloadCourse.course_type_id) {
                        course.type = val;
                        return;
                    }
                });

                angular.forEach(preloadBranches, function(val, index, self) {
                    if (val.id == preloadCourse.branch_id) {
                        course.variationCourses[0].selectedBranch = val;
                        return;
                    }
                });

                angular.forEach(preloadTeachers, function(val, index, self) {
                    if (val.id == preloadCourse.teacher_id) {
                        course.variationCourses[0].selectedTeacher = {
                            id: val.id,
                            name: val.first_name + ' ' + val.last_name
                        };
                    }

                    var tmp = {
                        id: val.id,
                        name: val.first_name + val.last_name
                    };

                    $scope.teachers.push(tmp);
                });

                if (courseEditCache) {
                    $scope.course = courseEditCache;
                } else {
                    $scope.course = course;
                    $scope.course.attachedImages = attachedImages;
                }
            };

            $scope.addImages = function () {
                $localStorage.courseEditCache = $scope.course;
                $state.go('admin.media-manager', {select: true});
            };

            $scope.addTypes = function () {
                $localStorage.adminCourseCache = $scope.course;
                $state.go('admin.course.type')
            };

            $scope.duplicate = function (variationCourseSrc) {
                var variationCourse = {};
                angular.copy(variationCourseSrc, variationCourse);
                $scope.course.variationCourses.push(variationCourse);
            };

            $scope.deleteVariationCourse = function (index) {
                $scope.course.variationCourses.splice(index, 1);
            };

            $scope.newVariationCourse = function () {
                var variationCourse = {};
                angular.copy(variationCourseTemplate, variationCourse);
                $scope.course.variationCourses.push(variationCourse);
            };

            $scope.saveCourse = function () {
                var waitToast = ngToast.create({
                    className: 'info',
                    content: '<div class="timer-loader"></div>Processing...'
                });
                var promise = AdminCourseService.variationCourseEdit($scope.course);
                promise.then(
                    function (data) {
                        ngToast.dismiss(waitToast);
                        ngToast.create({
                            className: 'success',
                            dismissOnTimeout: true,
                            content: 'Success!'
                        });

                        //delete $localStorage.courseEditCache;
                        //
                        //$state.transitionTo('admin.course.list', {}, {
                        //    reload: true
                        //});
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
            };

            $scope.thumbnailSetDefault = function (index) {
                var defaultImage = $scope.course.attachedImages[index];

                angular.forEach($scope.course.attachedImages, function (value, key, object) {
                    if (value.default == true) {
                        object[key].default = false;
                    }
                });

                defaultImage.default = true;
                $scope.course.showcaseDetail.defaultImage = defaultImage;

                $localStorage.defaultImageIndex = index;
            };

            $scope.thumbnailDelete = function (index) {
                var attachedImages = $scope.course.attachedImages;

                attachedImages.splice(index, 1);

                if (attachedImages.length == 0) {
                    delete $localStorage.defaultImageIndex;
                } else {
                    attachedImages[0].default = true;
                    $localStorage.defaultImageIndex = 0;
                }

            };

            $scope.updateSubtypes = function () {
                $scope.subtypes = $scope.course.type.subTypes;
            };

            $scope.init();
        }])
    .controller('AdminCourseTypeController',
    [        '$scope', '$http', '$log', '$localStorage', 'AdminCourseService',
        function ($scope,   $http,   $log,   $localStorage,   AdminCourseService) {
            $scope.page.title = 'Course Type List';
            $scope.csrf = {value: ''};
            $scope.uiTree = {
                maxDepth: 2
            };

            var promise = AdminCourseService.courseTypeList();
            promise.then(
                function (data) {
                    $scope.types = data.types;
                    $scope.maxId = data.maxId;
                },
                function (reason) {
                    $scope.serverError = reason;
                }
            );

            $scope.newType = function (scope) {
                $scope.types.push({id: ++$scope.maxId, name: '', subTypes: []});
            };

            $scope.newSubType = function (scope) {
                var node = scope.$modelValue;
                if (scope.depth() < $scope.uiTree.maxDepth) {
                    node.subTypes.push({id: ++$scope.maxId, name: '', subTypes: []});
                } else {
                    alert('Only support 2 level type structure. Type > Subtype');
                }
            };

            $scope.edit = function (scope) {
                var node = scope.$modelValue;
                node.edit = true;
            };

            $scope.editFinish = function (scope) {
                var node = scope.$modelValue;
                node.edit = false;
            };

            $scope.save = function () {
                var promise = AdminCourseService.updateType($scope.types);
                //promise.then(
                //    function (data) {
                //        $log.log(data);
                //    },
                //    function (reason) {
                //        $scope.serverError = reason;
                //    }
                //);
            };
        }]
);