'use strict';

app.factory('AdminProductService',
    [        '$http','$q',
    function ($http,  $q) {
        return {
            listCategory: function () {
                var deferred = $q.defer();
                $http({
                    method: 'GET',
                    url: 'product/category-list'
                })
                .success(function (data, status, headers, config) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, config) {
                    deferred.reject(data);
                });
                return deferred.promise;
            },
            updateCategory: function (categories) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'product/category-update',
                    data: {
                        categories: categories
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
            productCreate: function (product) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'product/product-save',
                    data: {
                        product: product
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
            productGet: function (variationProductId) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'product/product-get',
                    data: {
                        variationProductId: variationProductId
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
            variationProductEdit: function (product) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'product/product-update',
                    data: {
                        product: product
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
            variationProductDelete: function (variationProductId) {
                var deferred = $q.defer();
                $http({
                    method: 'POST',
                    url: 'product/product-terminate',
                    data: {
                        variationProductId: variationProductId
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
