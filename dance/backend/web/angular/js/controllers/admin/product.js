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
    [        '$scope', '$http', '$localStorage',
    function ($scope,   $http,   $localStorage) {
        $scope.page.title = 'Product Category List';
        $scope.csrf = {value: ''};
        $scope.categories = [
            {
                name: '1',
                subCategories: [
                    {
                        name: '1.1',
                        subCategories: []
                    },
                    {
                        name: '1.2',
                        subCategories: []
                    }
                ]
            },
            {
                name: '2',
                subCategories: [
                    {
                        name: '2.1',
                        subCategories: []
                    }
                ]
            },
        ];

        $scope.newCategory = function (scope) {
            $scope.categories.push({name: '', subCategories: []});
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

        };
    }]
);