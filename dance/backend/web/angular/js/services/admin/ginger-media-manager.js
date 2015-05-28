'use strict';

app.factory('GingerMediaManager',
    [        '$http','$q',
    function ($http,  $q) {
        return {
            init: function () {
                var deferred = $q.defer();
                $http({
                    method: 'GET',
                    url: 'image/list',
                })
                .success(function (data, status, headers, config) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, config) {
                    deferred.reject(data);
                });
                return deferred.promise;
            },
            delete: function (id) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'image/delete',
                    data: {
                        id: id
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