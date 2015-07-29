'use strict';

app.factory('AdminCourseService',
    [        '$http','$q',
        function ($http,  $q) {
            return {
                courseTypeList: function () {
                    var deferred = $q.defer();
                    $http({
                        method: 'GET',
                        url: 'course-type/type-list'
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                updateType: function (types) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'course-type/type-update',
                        data: {
                            types: types
                        }
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                courseCreate: function (course) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'course/course-save',
                        data: {
                            course: course
                        }
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                courseGet: function (variationCourseId) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'course/course-get',
                        data: {
                            variationCourseId: variationCourseId
                        }
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                variationCourseEdit: function (course) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'course/course-update',
                        data: {
                            course: course
                        }
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                variationCourseDelete: function (variationCourseId) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'course/course-terminate',
                        data: {
                            variationCourseId: variationCourseId
                        }
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                }
            };
        }]
);
