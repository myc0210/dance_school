'use strict'

app.factory('AdminBranchService',
    [        '$http','$q',
        function ($http,  $q) {
            return {
                branchCreate: function(branch) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'branch/branch-create',
                        data: {
                            branch: branch
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
                branchUpdate: function(branch) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'branch/branch-update',
                        data: {
                            branchId: branch.id,
                            branch: branch
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
                branchGet: function (branchId) {
                    var deferred = $q.defer();
                    $http({
                        url: 'branch/branch-get/?branchId=' + branchId,
                    })
                        .success(function (data, status, headers, config) {
                            deferred.resolve(data);
                        })
                        .error(function (data, status, headers, config) {
                            deferred.reject(data);
                        });
                    return deferred.promise;
                },
                branchDelete: function(branchId) {
                    var deferred = $q.defer();
                    $http({
                        method: 'POST',
                        url: 'branch/branch-terminate',
                        data: {
                            branchId: branchId
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
                branchList: function() {
                    var deferred = $q.defer();
                    $http({
                        url: 'branch/branch-list'
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