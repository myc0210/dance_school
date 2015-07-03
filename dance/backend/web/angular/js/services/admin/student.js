'use strict'

app.factory('AdminStudentService',
    [        '$http','$q',
    function ($http,  $q) {
        return {
            studentCreate: function(student) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'student/student-create',
                    data: {
                        student: student
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
            studentDelete: function(studentId) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'student/student-delete',
                    data: {
                        studentId: studentId
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
        }
    }]
)