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
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page.title = 'Add New Product';
        $scope.product = {
            name: '',
            code: '',
            color: 'N/A',
            size: 'N/A',
            price: '0.00',
            cost: '0.00',
            description: ''
        };
        $scope.csrf = {value: ''};

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

        var promise = AdminProductService.init();
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
            var promise = AdminProductService.save($scope.categories);
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