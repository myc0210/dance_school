'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;        
      }
    ]
  )
  .config(
    [          '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 
      function ($stateProvider,   $urlRouterProvider, JQ_CONFIG) {
          
          $urlRouterProvider
              .otherwise('/access/signin');
              
          $stateProvider
              .state('admin', {
                  abstract: true,
                  url: '/admin',
                  templateUrl: 'tpl/admin/app.html'
              })
              .state('admin.dashboard', {
                  url: '/dashboard',
                  templateUrl: 'tpl/admin/dashboard.html'
              })
              .state('admin.media-manager', {
                  url: '/media-manager/{select}',
                  templateUrl: 'tpl/admin/media_manager.html',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ($ocLazyLoad) {
                              return $ocLazyLoad.load('ng-file-upload');
                          }
                      ]
                  }
              })
              .state('admin.product', {
                  abstract: true,
                  url: '/product',
                  templateUrl: 'tpl/admin/product/general.html',
                  controller: 'AdminProductGeneralController'
              })
              .state('admin.product.list', {
                  url: '/list',
                  templateUrl: 'tpl/admin/product/list.html',
                  controller: 'AdminProductListController'
              })
              .state('admin.product.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/product/create.html',
                  controller: 'AdminProductCreateController',
                  resolve: {
                      preloadCategories: ['AdminProductService', function(AdminProductService) {
                          return AdminProductService.listCategory();
                      }],
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              //return $ocLazyLoad.load('ui.select');
                          }]
                  }
              })
              .state('admin.product.edit', {
                  url: '/edit/:productId',
                  templateUrl: 'csrftemplate/?url=admin/product/edit.html',
                  controller: 'AdminProductUpdateController',
                  resolve: {
                      preloadProduct: ['AdminProductService', '$stateParams', function (AdminProductService, $stateParams) {
                          return AdminProductService.productGet($stateParams.productId);
                      }],
                      preloadCategories: ['AdminProductService', function(AdminProductService) {
                          return AdminProductService.listCategory();
                      }]
                  }
              })
              .state('admin.product.category', {
                  url: '/category',
                  templateUrl: 'csrftemplate/?url=admin/product/type.html',
                  controller: 'AdminProductCategoryController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ($ocLazyLoad) {
                              return $ocLazyLoad.load('ui.tree');
                          }
                      ]
                  }
              })
              .state('admin.course', {
                  abstract: true,
                  url: '/course',
                  templateUrl: 'tpl/admin/course/general.html',
                  controller: 'AdminCourseGeneralController'
              })
              .state('admin.course.list', {
                  url: '/list',
                  templateUrl: 'tpl/admin/course/list.html',
                  controller: 'AdminCourseListController'
              })
              .state('admin.course.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/course/create.html',
                  controller: 'AdminCourseCreateController',
                  resolve: {
                      preloadTypes: ['AdminCourseService', function(AdminCourseService) {
                          return AdminCourseService.courseTypeList();
                      }],
                      preloadBranches: ['AdminBranchService', function(AdminBranchService) {
                          return AdminBranchService.branchList();
                      }],
                      preloadTeachers: ['AdminTeacherService', function(AdminTeacherService) {
                          return AdminTeacherService.teacherList();
                      }],
                      deps: ['$ocLazyLoad',
                          function( $ocLazyLoad ){
                              //return $ocLazyLoad.load('ui.select');
                          }]
                  }
              })
              .state('admin.course.edit', {
                  url: '/edit/:courseId',
                  templateUrl: 'csrftemplate/?url=admin/course/edit.html',
                  controller: 'AdminCourseUpdateController',
                  resolve: {
                      preloadCourse: ['AdminCourseService', '$stateParams', function (AdminCourseService, $stateParams) {
                          return AdminCourseService.courseGet($stateParams.courseId);
                      }],
                      preloadTypes: ['AdminCourseService', function(AdminCourseService) {
                          return AdminCourseService.courseTypeList();
                      }],
                      preloadBranches: ['AdminBranchService', function(AdminBranchService) {
                          return AdminBranchService.branchList();
                      }],
                      preloadTeachers: ['AdminTeacherService', function(AdminTeacherService) {
                          return AdminTeacherService.teacherList();
                      }]
                  }
              })
              .state('admin.course.type', {
                  url: '/type',
                  templateUrl: 'csrftemplate/?url=admin/course/type.html',
                  controller: 'AdminCourseTypeController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                          function ($ocLazyLoad) {
                              return $ocLazyLoad.load('ui.tree');
                          }
                      ]
                  }
              })
              .state('admin.teacher', {
                  abstract:true,
                  url: '/teacher',
                  templateUrl: 'tpl/admin/teacher/general.html',
                  controller: 'AdminTeacherGeneralController'
              })
              .state('admin.teacher.list', {
                  url: '/list',
                  templateUrl: 'csrftemplate/?url=admin/teacher/list.html',
                  controller: 'AdminTeacherListController',
                  resolve: {
                  }
              })
              .state('admin.teacher.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/teacher/create.html',
                  controller: 'AdminTeacherCreateController',
                  resolve: {
                  }
              })
              .state('admin.teacher.edit', {
                  url: '/edit/:teacherId',
                  templateUrl: 'csrftemplate/?url=admin/teacher/edit.html',
                  controller: 'AdminTeacherEditController',
                  resolve: {
                      teacher: ['AdminTeacherService', '$stateParams', function (AdminTeacherService, $stateParams) {
                        return AdminTeacherService.teacherGet($stateParams.teacherId);
                      }]
                  }
              })
              .state('admin.school', {
                  abstract:true,
                  url: '/school',
                  templateUrl: 'tpl/admin/school/general.html',
                  controller: 'AdminSchoolGeneralController'
              })
              .state('admin.school.list', {
                  url: '/list',
                  templateUrl: 'csrftemplate/?url=admin/school/list.html',
                  controller: 'AdminSchoolListController',
                  resolve: {
                  }
              })
              .state('admin.school.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/school/create.html',
                  controller: 'AdminSchoolCreateController',
                  resolve: {
                  }
              })
              .state('admin.school.edit', {
                  url: '/edit/:schoolId',
                  templateUrl: 'csrftemplate/?url=admin/school/edit.html',
                  controller: 'AdminSchoolEditController',
                  resolve: {
                      school: ['AdminSchoolService', '$stateParams', function (AdminSchoolService, $stateParams) {
                          return AdminSchoolService.schoolGet($stateParams.schoolId);
                      }]
                  }
              })
              .state('admin.branch', {
                  abstract:true,
                  url: '/branch',
                  templateUrl: 'tpl/admin/branch/general.html',
                  controller: 'AdminBranchGeneralController'
              })
              .state('admin.branch.list', {
                  url: '/list',
                  templateUrl: 'csrftemplate/?url=admin/branch/list.html',
                  controller: 'AdminBranchListController',
                  resolve: {
                  }
              })
              .state('admin.branch.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/branch/create.html',
                  controller: 'AdminBranchCreateController',
                  resolve: {
                      schools: ['AdminSchoolService', function(AdminSchoolService) {
                          return AdminSchoolService.schoolGetAll();
                      }]
                  }
              })
              .state('admin.branch.edit', {
                  url: '/edit/:branchId',
                  templateUrl: 'csrftemplate/?url=admin/branch/edit.html',
                  controller: 'AdminBranchEditController',
                  resolve: {
                      schools: ['AdminSchoolService', function(AdminSchoolService) {
                          return AdminSchoolService.schoolGetAll();
                      }],
                      branch: ['AdminBranchService', '$stateParams', function(AdminBranchService, $stateParams) {
                          return AdminBranchService.branchGet($stateParams.branchId);
                      }]
                  }
              })
              .state('admin.student', {
                  abstract: true,
                  url: '/student',
                  templateUrl: 'tpl/admin/student/general.html',
                  controller: 'AdminStudentGeneralController'
              })
              .state('admin.student.list', {
                  url: '/list',
                  templateUrl: 'csrftemplate/?url=admin/student/list.html',
                  controller: 'AdminStudentListController',
                  resolve: {
                  }
              })
              .state('admin.student.create', {
                  url: '/create',
                  templateUrl: 'csrftemplate/?url=admin/student/create.html',
                  controller: 'AdminStudentCreateController',
                  resolve: {
                      promiseSchools: ['AdminSchoolService', function(AdminSchoolService) {
                          return AdminSchoolService.schoolGetAll();
                      }],
                      promiseBranches: ['AdminBranchService', function(AdminBranchService) {
                          return AdminBranchService.branchList();
                      }]
                  }
              })
              .state('admin.student.trail', {
                  url: '/trail/:studentId',
                  templateUrl: 'csrftemplate/?url=admin/student/trail.html',
                  resolve: {
                      preloadCategories: ['AdminProductService', '$stateParams', function(AdminProductService, $stateParams) {
                          return AdminProductService.getStudent($stateParams.studentId);
                      }]
                  }
              })
              // others
              .state('lockme', {
                  url: '/lockme',
                  templateUrl: 'tpl/page_lockme.html'
              })
              .state('access', {
                  url: '/access',
                  template: '<div ui-view class="fade-in-right-big smooth"></div>'
              })
              .state('access.signin', {
                  url: '/signin',
                  templateUrl: 'csrftemplate/?url=page_signin.html',
                  resolve: {
                      deps: ['uiLoad',
                        function( uiLoad ){
                          return uiLoad.load( ['js/controllers/signin.js'] );
                      }]
                  }
              })
              .state('access.signup', {
                  url: '/signup',
                  templateUrl: 'tpl/page_signup.html',
                  resolve: {
                      deps: ['uiLoad',
                        function( uiLoad ){
                          return uiLoad.load( ['js/controllers/signup.js'] );
                      }]
                  }
              })
              .state('access.forgotpwd', {
                  url: '/forgotpwd',
                  templateUrl: 'template/?url=page_forgotpwd.html'
              })
              .state('access.404', {
                  url: '/404',
                  templateUrl: 'tpl/page_404.html'
              })
      }
    ]
  );
