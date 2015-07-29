'use strict'

app.factory('AdminTeacherService',
    [        '$http','$q',
    function ($http,  $q) {
        return {
            teacherCreate: function(teacher) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'teacher/teacher-create',
                    data: {
                        teacher: teacher
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
            teacherUpdate: function(teacher) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'teacher/teacher-update',
                    data: {
                        teacherId: teacher.id,
                        teacher: teacher
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
            teacherGet: function (teacherId) {
                var deferred = $q.defer();
                $http({
                    url: 'teacher/teacher-get/?teacherId=' + teacherId,
                })
                    .success(function (data, status, headers, config) {
                        deferred.resolve(data);
                    })
                    .error(function (data, status, headers, config) {
                        deferred.reject(data);
                    });
                return deferred.promise;
            },
            teacherDelete: function(teacherId) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'teacher/teacher-terminate',
                    data: {
                        teacherId: teacherId
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
            teacherList: function() {
                var deferred = $q.defer();
                $http({
                    url: 'teacher/teacher-list'
                })
                    .success(function (data, status, headers, config) {
                        deferred.resolve(data);
                    })
                    .error(function (data, status, headers, config) {
                        deferred.reject(data);
                    });
                return deferred.promise;
            }
        }
    }]
)