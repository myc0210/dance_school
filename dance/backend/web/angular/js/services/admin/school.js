'use strict'

app.factory('AdminSchoolService',
    [        '$http','$q',
        function ($http,  $q) {
            return {
                schoolCreate: function(school) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'school/school-create',
                        data: {
                            school: school
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
                schoolUpdate: function(school) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'school/school-update',
                        data: {
                            schoolId: school.id,
                            school: school
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
                schoolGetAll: function() {
                    var deferred = $q.defer();
                    $http({
                        url: 'school/school-list'
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                schoolGet: function(schoolId) {
                    var deferred = $q.defer();
                    $http({
                        url: 'school/school-get/?schoolId=' + schoolId,
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                schoolDelete: function(schoolId) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'school/school-terminate',
                        data: {
                            schoolId: schoolId
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