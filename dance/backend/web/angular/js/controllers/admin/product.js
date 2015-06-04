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
    [        '$scope', '$http', '$timeout',
    function ($scope,   $http,   $timeout) {
        $scope.page.title = 'Product List';
        $scope.product = {};
        $scope.dtOption = {
            sAjaxSource: 'api/datatable.json',
            aoColumns: [
                { mData: 'engine' },
                { mData: 'browser' },
                { mData: 'platform' },
                { mData: 'version' },
                { mData: 'grade' }
            ]
        };
    }]
)
.controller('ProductAddController',
    [        '$scope', '$http', '$state', '$localStorage', 'toaster', 'AdminProductService', 'preloadCategories',
    function ($scope,   $http,   $state,   $localStorage,   toaster,   AdminProductService,   preloadCategories) {
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
                    category: 0,
                    subcategory: 0
                };
            }

            $scope.categories = preloadCategories.categories;
            $scope.subcategories = [];

            if (attachedImages) {
                $scope.product.attachedImages = attachedImages;
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

        $scope.duplicate = function (variationProductSrc) {
            var variationProduct = {};
            angular.copy(variationProductSrc, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.deleteVariationProduct = function (index) {
            $scope.product.variationProducts.splice(index, 1);
        };

        $scope.newVariationProduct = function () {
            var variationProduct = {};
            angular.copy(variationProductTemplate, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.saveProduct = function () {
            toaster.pop('wait', 'empty', 'empty', 0);
            var promise = AdminProductService.saveProduct($scope.product);
            promise.then(
                function (data) {
                    toaster.clear();
                },
                function (reason) {
                    $scope.serverError = reason;
                }
            );
        };

        $scope.thumbnailSetDefault = function (index) {
            var defaultImage = $scope.product.attachedImages[index];

            angular.forEach($scope.product.attachedImages, function (value, key, object) {
                if (value.default == true) {
                    object[key].default = false;
                }
            });

            defaultImage.default = true;
            $scope.product.showcaseDetail.image = defaultImage;

            $localStorage.defaultImageIndex = index;
        };

        $scope.thumbnailDelete = function (index) {
            var attachedImages = $scope.product.attachedImages;

            attachedImages.splice(index, 1);

            if (attachedImages.length == 0) {
                delete $localStorage.defaultImageIndex;
            } else {
                attachedImages[0].default = true;
                $localStorage.defaultImageIndex = 0;
            }

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