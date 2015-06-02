'use strict';

app.controller('ProductGeneralController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page = {
            title: ''
        };
    }]
)
.controller('ProductListController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page.title = 'Product List';
        $scope.product = {};

    }]
)
.controller('ProductAddController',
    [        '$scope', '$http', '$state', '$localStorage', 'preloadCategories',
    function ($scope,   $http,   $state,   $localStorage,   preloadCategories) {
        var variationProductTemplate = {
                code: '',
                level: '',
                color: '',
                size: '',
                cost: '',
                price: ''
            };
        $scope.page.title = 'Add New Product';
        $scope.csrf = {value: ''};
        $scope.attachedImages = [];

        $scope.init = function () {
            var variationProduct = {},
                productCache = $localStorage.adminProductCache,
                attachedImages = $localStorage.attachedImages;
            angular.copy(variationProductTemplate, variationProduct);

            if (productCache) {
                $scope.product = productCache;
            } else {
                $scope.product = {
                    name: '',
                    variationProducts: [
                        variationProduct,
                    ],
                    description: '',
                    category: '',
                    subcategory: '',
                };
            }

            $scope.categories = preloadCategories.categories;
            $scope.subcategories = [];

            if (attachedImages) {
                $scope.attachedImages = attachedImages;
            }
        };

        $scope.addImages = function () {
            $localStorage.adminProductCache = $scope.product;
            $state.go('admin.media-manager', {select: true});
        };

        $scope.addCategories = function () {
            $localStorage.adminProductCache = $scope.product;
            $state.go('admin.product.category')
        };

        $scope.newVariationProduct = function () {
            var variationProduct = {};
            angular.copy(variationProductTemplate, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.duplicate = function (variationProductSrc) {
            var variationProduct = {};
            angular.copy(variationProductSrc, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.delete = function (index) {
            $scope.product.variationProducts.splice(index, 1);
        };

        $scope.thumbnailSetDefault = function (index) {
            angular.forEach($scope.attachedImages, function (value, key, object) {
                if (value.default == true) {
                    object[key].default = false;
                }
            });
            $scope.attachedImages[index].default = true;
            $localStorage.defaultImageIndex = index;
        };

        $scope.thumbnailDelete = function (index) {
            $scope.attachedImages.splice(index, 1);
        };

        $scope.updateSubcategories = function () {
            console.log($scope.product.category);
            $scope.subcategories = $scope.product.category.subCategories;
        };

        $scope.init();
    }]
)
.controller('ProductCategoryController',
    [        '$scope', '$http', '$log', '$localStorage', 'AdminProductService',
    function ($scope,   $http,   $log,   $localStorage,   AdminProductService) {
        $scope.page.title = 'Product Category List';
        $scope.csrf = {value: ''};
        $scope.uiTree = {
            maxDepth: 2
        };

        var promise = AdminProductService.listCategory();
        promise.then(
            function (data) {
                $scope.categories = data.categories;
                $scope.maxId = data.maxId;
            },
            function (reason) {
                $scope.serverError = reason;
            }
        );

        $scope.newCategory = function (scope) {
            $scope.categories.push({id: ++$scope.maxId, name: '', subCategories: []});
        };

        $scope.newSubCategory = function (scope) {
            var node = scope.$modelValue;
            if (scope.depth() < $scope.uiTree.maxDepth) {
                node.subCategories.push({id: ++$scope.maxId, name: '', subCategories: []});
            } else {
                alert('Only support 2 level category structure. Category > Subcategory');
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
            var promise = AdminProductService.updateCategory($scope.categories);
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